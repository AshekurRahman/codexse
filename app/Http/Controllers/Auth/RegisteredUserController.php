<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        // Store referral code in session if provided
        if ($request->has('ref')) {
            $referrer = User::where('referral_code', $request->ref)->first();
            if ($referrer) {
                session(['referral_code' => $request->ref]);
            }
        }

        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Check for referral code
        $referredBy = null;
        $referralCode = $request->input('referral_code') ?? session('referral_code');

        if ($referralCode) {
            $referrer = User::where('referral_code', $referralCode)->first();
            if ($referrer) {
                $referredBy = $referrer->id;
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'referred_by' => $referredBy,
        ]);

        // Process referral rewards
        if ($referredBy) {
            $user->processReferralSignupReward();
            session()->forget('referral_code');
        }

        event(new Registered($user));

        Auth::login($user);

        // Redirect to email verification notice page instead of dashboard
        // since dashboard requires verified email
        $redirectUrl = route('verification.notice');

        if ($request->expectsJson()) {
            return response()->json(['redirect' => $redirectUrl]);
        }

        return redirect($redirectUrl);
    }
}
