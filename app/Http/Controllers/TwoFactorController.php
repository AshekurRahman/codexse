<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorController extends Controller
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Show 2FA settings page
     */
    public function index()
    {
        $user = auth()->user();

        return view('auth.two-factor.index', [
            'enabled' => $user->hasTwoFactorEnabled(),
            'recoveryCodes' => $user->hasTwoFactorEnabled() ? $user->getRecoveryCodes() : [],
        ]);
    }

    /**
     * Show setup page with QR code
     */
    public function setup()
    {
        $user = auth()->user();

        if ($user->hasTwoFactorEnabled()) {
            return redirect()->route('two-factor.index')
                ->with('info', 'Two-factor authentication is already enabled.');
        }

        // Generate secret
        $secret = $this->google2fa->generateSecretKey();
        $user->two_factor_secret = encrypt($secret);
        $user->save();

        // Generate QR code
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($qrCodeUrl);

        return view('auth.two-factor.setup', [
            'qrCodeSvg' => $qrCodeSvg,
            'secret' => $secret,
        ]);
    }

    /**
     * Enable 2FA after verifying code
     */
    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = auth()->user();
        $secret = $user->getTwoFactorSecret();

        if (!$secret) {
            return back()->with('error', 'Please start the setup process again.');
        }

        $valid = $this->google2fa->verifyKey($secret, $request->code);

        if (!$valid) {
            return back()->with('error', 'Invalid verification code. Please try again.');
        }

        // Enable 2FA
        $user->two_factor_enabled = true;
        $user->two_factor_confirmed_at = now();
        $user->save();

        // Generate recovery codes
        $codes = $user->generateRecoveryCodes();

        return redirect()->route('two-factor.index')
            ->with('success', 'Two-factor authentication has been enabled.')
            ->with('recoveryCodes', $codes);
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = auth()->user();
        $user->two_factor_enabled = false;
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        return redirect()->route('two-factor.index')
            ->with('success', 'Two-factor authentication has been disabled.');
    }

    /**
     * Regenerate recovery codes
     */
    public function regenerateCodes(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = auth()->user();

        if (!$user->hasTwoFactorEnabled()) {
            return back()->with('error', 'Two-factor authentication is not enabled.');
        }

        $codes = $user->generateRecoveryCodes();

        return back()
            ->with('success', 'Recovery codes have been regenerated.')
            ->with('recoveryCodes', $codes);
    }

    /**
     * Show 2FA challenge page (after login)
     */
    public function challenge()
    {
        if (!session('2fa:user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor.challenge');
    }

    /**
     * Verify 2FA code during login
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $userId = session('2fa:user_id');

        if (!$userId) {
            return redirect()->route('login');
        }

        $user = \App\Models\User::find($userId);

        if (!$user) {
            session()->forget('2fa:user_id');
            return redirect()->route('login');
        }

        $code = str_replace(' ', '', $request->code);

        // Check if it's a recovery code
        if (strlen($code) === 10) {
            if ($user->useRecoveryCode($code)) {
                session()->forget('2fa:user_id');
                auth()->login($user, session('2fa:remember', false));
                session()->forget('2fa:remember');

                return redirect()->intended(route('dashboard'))
                    ->with('warning', 'You used a recovery code. Please regenerate your codes.');
            }
        }

        // Verify TOTP code
        $secret = $user->getTwoFactorSecret();
        $valid = $this->google2fa->verifyKey($secret, $code);

        if (!$valid) {
            return back()->with('error', 'Invalid verification code.');
        }

        session()->forget('2fa:user_id');
        auth()->login($user, session('2fa:remember', false));
        session()->forget('2fa:remember');

        return redirect()->intended(route('dashboard'));
    }
}
