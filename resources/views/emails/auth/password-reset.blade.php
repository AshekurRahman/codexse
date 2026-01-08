@extends('emails.layouts.base')

@section('title', 'Reset Your Password')

@section('preview')
Reset your {{ config('app.name') }} password. This link expires in 60 minutes.
@endsection

@section('hero')
<div style="background: #ffffff; padding: 40px; text-align: center;">
    <!-- Lock Icon -->
    <div style="width: 96px; height: 96px; background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); border-radius: 24px; margin: 0 auto 24px; box-shadow: 0 16px 32px -8px rgba(245, 158, 11, 0.4);">
        <table role="presentation" width="100%" height="100%">
            <tr>
                <td align="center" valign="middle">
                    <img src="{{ asset('images/email/lock.webp') }}" alt="Lock" style="width: 48px; height: 48px;">
                </td>
            </tr>
        </table>
    </div>
    <h1 style="font-size: 28px; font-weight: 700; color: #0f172a; margin: 0 0 12px; line-height: 1.3;">Reset Your Password</h1>
    <p style="font-size: 16px; color: #64748b; margin: 0;">We received a request to reset your password</p>
</div>
@endsection

@section('content')
<div style="padding: 0 40px 40px;">
    <!-- Greeting -->
    <p style="font-size: 16px; color: #334155; margin: 0 0 24px;">
        Hi there,
    </p>

    <!-- Main Text -->
    <p style="font-size: 16px; color: #334155; margin: 0 0 24px;">
        Someone requested a password reset for the {{ config('app.name') }} account associated with this email address. If this was you, click the button below to reset your password.
    </p>

    <!-- Info Box -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 1px solid #fcd34d; border-radius: 12px; margin-bottom: 24px;">
        <tr>
            <td style="padding: 20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="width: 24px; vertical-align: top; padding-right: 12px;">
                            <img src="{{ asset('images/email/alert-circle.webp') }}" alt="Warning" style="width: 24px; height: 24px;">
                        </td>
                        <td>
                            <p style="color: #92400e; margin: 0; font-size: 14px; line-height: 1.6;">
                                This link will expire in <strong>60 minutes</strong>. If you didn't request a password reset, you can safely ignore this email.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- CTA Button -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td align="center" style="padding: 16px 0 32px;">
                <a href="{{ $resetUrl }}" style="display: inline-block; padding: 18px 48px; background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 10px; box-shadow: 0 4px 14px -3px rgba(245, 158, 11, 0.5);">
                    <img src="{{ asset('images/email/key.webp') }}" alt="" style="width: 18px; height: 18px; vertical-align: middle; margin-right: 8px;">
                    Reset Password
                </a>
            </td>
        </tr>
    </table>

    <!-- Divider -->
    <div style="height: 1px; background: linear-gradient(90deg, transparent 0%, #e2e8f0 50%, transparent 100%); margin: 0 0 24px;"></div>

    <!-- Alternative Link -->
    <p style="font-size: 14px; color: #64748b; margin: 0 0 12px;">
        If the button above doesn't work, copy and paste this link into your browser:
    </p>
    <p style="font-size: 12px; color: #6366f1; margin: 0 0 24px; word-break: break-all; background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;">
        {{ $resetUrl }}
    </p>

    <!-- Divider -->
    <div style="height: 1px; background: linear-gradient(90deg, transparent 0%, #e2e8f0 50%, transparent 100%); margin: 0 0 24px;"></div>

    <!-- Support -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">
        <tr>
            <td style="padding: 20px; text-align: center;">
                <p style="color: #64748b; font-size: 14px; margin: 0 0 8px;">Need help? We're here for you!</p>
                <a href="{{ url('/support') }}" style="color: #6366f1; font-size: 14px; text-decoration: none; font-weight: 500;">
                    <img src="{{ asset('images/email/message.webp') }}" alt="" style="width: 16px; height: 16px; vertical-align: middle; margin-right: 6px;">
                    Contact Support
                </a>
            </td>
        </tr>
    </table>
</div>
@endsection
