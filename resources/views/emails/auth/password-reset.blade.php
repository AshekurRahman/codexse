@extends('emails.layouts.base')

@section('title', 'Reset Your Password')

@section('preview')
Reset your Codexse password. This link expires in 60 minutes.
@endsection

@section('additional_styles')
<style>
    .header-gradient {
        background: linear-gradient(135deg, #f59e0b 0%, #f97316 50%, #ef4444 100%);
        position: relative;
        overflow: hidden;
    }
    .security-notice {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 1px solid #fcd34d;
        border-radius: 12px;
        padding: 16px;
        margin: 24px 0;
    }
</style>
@endsection

@section('hero')
<div style="background: #ffffff;">
    <div style="text-align: center; padding: 20px 40px 32px;">
        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); border-radius: 20px; margin: 0 auto 24px; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.4);">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15 7C16.1046 7 17 7.89543 17 9V11H7V9C7 7.89543 7.89543 7 9 7M15 7H9M15 7C15 5.89543 14.1046 5 13 5H11C9.89543 5 9 5.89543 9 7M19 11V19C19 20.1046 18.1046 21 17 21H7C5.89543 21 5 20.1046 5 19V11M19 11H5M12 15V17" stroke="#ffffff" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
        <h1 style="font-size: 28px; font-weight: 700; color: #111827; margin: 0 0 8px;">Reset Your Password</h1>
        <p style="font-size: 16px; color: #6b7280; margin: 0;">We received a request to reset your password</p>
    </div>
</div>
@endsection

@section('content')
<div class="email-content">
    <p style="color: #374151; font-size: 16px; line-height: 1.6; margin: 0 0 24px;">
        Hello,<br><br>
        Someone requested a password reset for the Codexse account associated with this email address. If this was you, click the button below to reset your password.
    </p>

    <!-- CTA Button -->
    <div style="text-align: center; margin: 32px 0;">
        <a href="{{ $resetUrl }}" style="display: inline-block; padding: 18px 48px; background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); color: #ffffff; font-size: 18px; font-weight: 600; text-decoration: none; border-radius: 12px; box-shadow: 0 8px 20px -4px rgba(245, 158, 11, 0.5);">
            Reset Password
        </a>
    </div>

    <!-- Expiry Notice -->
    <div style="background: #f9fafb; border-radius: 10px; padding: 16px; text-align: center; margin: 24px 0;">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="text-align: center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                        <path d="M12 8V12L15 15M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span style="color: #6b7280; font-size: 14px;">This link will expire in <strong>60 minutes</strong></span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Security Notice -->
    <div class="security-notice">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="width: 40px; vertical-align: top;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#d97706" stroke-width="2"/>
                        <path d="M12 8V12M12 16H12.01" stroke="#d97706" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </td>
                <td style="padding-left: 12px;">
                    <p style="font-weight: 600; color: #92400e; margin: 0 0 4px; font-size: 14px;">Didn't request this?</p>
                    <p style="color: #a16207; margin: 0; font-size: 13px;">If you didn't request a password reset, you can safely ignore this email. Your password will not be changed.</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="divider"></div>

    <!-- Alternative Link -->
    <p style="color: #6b7280; font-size: 13px; margin: 0; text-align: center;">
        If the button doesn't work, copy and paste this link into your browser:
    </p>
    <p style="color: #6366f1; font-size: 12px; margin: 8px 0 0; text-align: center; word-break: break-all;">
        {{ $resetUrl }}
    </p>
</div>
@endsection
