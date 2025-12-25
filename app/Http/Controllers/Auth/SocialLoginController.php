<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialLoginSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    protected array $providers = ['google', 'facebook', 'github', 'twitter'];

    public function redirect(string $provider)
    {
        if (!in_array($provider, $this->providers)) {
            abort(404);
        }

        if (!SocialLoginSetting::isProviderEnabled($provider)) {
            return redirect()->route('login')
                ->with('error', 'This login method is not available.');
        }

        $config = SocialLoginSetting::getProviderConfig($provider);
        config(["services.{$provider}" => $config]);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider)
    {
        if (!in_array($provider, $this->providers)) {
            abort(404);
        }

        if (!SocialLoginSetting::isProviderEnabled($provider)) {
            return redirect()->route('login')
                ->with('error', 'This login method is not available.');
        }

        $config = SocialLoginSetting::getProviderConfig($provider);
        config(["services.{$provider}" => $config]);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Failed to authenticate with ' . SocialLoginSetting::getProviderLabel($provider) . '. Please try again.');
        }

        $providerId = $provider . '_id';

        // Check if user already exists with this social ID
        $user = User::where($providerId, $socialUser->getId())->first();

        if (!$user) {
            // Check if user exists with same email
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                // Link social account to existing user
                $user->update([
                    $providerId => $socialUser->getId(),
                    'social_avatar' => $user->avatar ? null : $socialUser->getAvatar(),
                ]);
            } else {
                // Create new user
                $user = User::create([
                    'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                    'email' => $socialUser->getEmail(),
                    'email_verified_at' => now(),
                    $providerId => $socialUser->getId(),
                    'social_avatar' => $socialUser->getAvatar(),
                    'password' => null,
                ]);
            }
        } else {
            // Update avatar if not set
            if (!$user->avatar && !$user->social_avatar && $socialUser->getAvatar()) {
                $user->update(['social_avatar' => $socialUser->getAvatar()]);
            }
        }

        Auth::login($user, true);

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Welcome back, ' . $user->name . '!');
    }
}
