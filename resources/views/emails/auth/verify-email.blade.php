@extends('emails.layouts.base')

@section('title', 'Verify Your Email Address')

@section('preview')
Please verify your email address to complete your {{ config('app.name') }} registration.
@endsection

@section('hero')
<div style="background: #ffffff; padding: 40px; text-align: center;">
    <!-- Verify Icon -->
    <div style="width: 96px; height: 96px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border-radius: 24px; margin: 0 auto 24px; box-shadow: 0 16px 32px -8px rgba(99, 102, 241, 0.4);">
        <table role="presentation" width="100%" height="100%">
            <tr>
                <td align="center" valign="middle">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 8L10.89 13.26C11.2187 13.4793 11.6049 13.5963 12 13.5963C12.3951 13.5963 12.7813 13.4793 13.11 13.26L21 8M5 19H19C19.5304 19 20.0391 18.7893 20.4142 18.4142C20.7893 18.0391 21 17.5304 21 17V7C21 6.46957 20.7893 5.96086 20.4142 5.58579C20.0391 5.21071 19.5304 5 19 5H5C4.46957 5 3.96086 5.21071 3.58579 5.58579C3.21071 5.96086 3 6.46957 3 7V17C3 17.5304 3.21071 18.0391 3.58579 18.4142C3.96086 18.7893 4.46957 19 5 19Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </td>
            </tr>
        </table>
    </div>
    <h1 style="font-size: 28px; font-weight: 700; color: #0f172a; margin: 0 0 12px; line-height: 1.3;">Verify Your Email</h1>
    <p style="font-size: 16px; color: #64748b; margin: 0;">One more step to complete your registration</p>
</div>
@endsection

@section('content')
<div style="padding: 0 40px 40px;">
    <!-- Greeting -->
    <p style="font-size: 16px; color: #334155; margin: 0 0 24px;">
        Hi {{ $user->name ?? 'there' }},
    </p>

    <!-- Main Text -->
    <p style="font-size: 16px; color: #334155; margin: 0 0 24px;">
        Thanks for signing up for {{ config('app.name') }}! Please click the button below to verify your email address and activate your account.
    </p>

    <!-- Info Box -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 1px solid #93c5fd; border-radius: 12px; margin-bottom: 24px;">
        <tr>
            <td style="padding: 20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="width: 24px; vertical-align: top; padding-right: 12px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 16V12M12 8H12.01" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </td>
                        <td>
                            <p style="color: #1e40af; margin: 0; font-size: 14px; line-height: 1.6;">
                                This verification link will expire in <strong>60 minutes</strong>. If you didn't create an account, you can safely ignore this email.
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
                <a href="{{ $verificationUrl }}" style="display: inline-block; padding: 18px 48px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 10px; box-shadow: 0 4px 14px -3px rgba(99, 102, 241, 0.5);">
                    Verify Email Address
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
        {{ $verificationUrl }}
    </p>

    <!-- Divider -->
    <div style="height: 1px; background: linear-gradient(90deg, transparent 0%, #e2e8f0 50%, transparent 100%); margin: 0 0 24px;"></div>

    <!-- What's Next Section -->
    <p style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px;">After verification, you can:</p>

    <!-- Features List -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 24px;">
        <tr>
            <td style="padding: 8px 0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="width: 28px; vertical-align: top;">
                            <div style="width: 20px; height: 20px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 50%;">
                                <table role="presentation" width="100%" height="100%">
                                    <tr>
                                        <td align="center" valign="middle">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M20 6L9 17L4 12" stroke="#ffffff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                        <td style="padding-left: 12px;">
                            <p style="color: #334155; margin: 0; font-size: 14px;">Browse and purchase premium digital products</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="width: 28px; vertical-align: top;">
                            <div style="width: 20px; height: 20px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 50%;">
                                <table role="presentation" width="100%" height="100%">
                                    <tr>
                                        <td align="center" valign="middle">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M20 6L9 17L4 12" stroke="#ffffff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                        <td style="padding-left: 12px;">
                            <p style="color: #334155; margin: 0; font-size: 14px;">Hire talented freelancers for your projects</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="width: 28px; vertical-align: top;">
                            <div style="width: 20px; height: 20px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 50%;">
                                <table role="presentation" width="100%" height="100%">
                                    <tr>
                                        <td align="center" valign="middle">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M20 6L9 17L4 12" stroke="#ffffff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                        <td style="padding-left: 12px;">
                            <p style="color: #334155; margin: 0; font-size: 14px;">Become a seller and start earning</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Support -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="text-align: center;">
                <p style="color: #64748b; font-size: 14px; margin: 0 0 8px;">Need help? We're here for you!</p>
                <a href="{{ url('/support') }}" style="color: #6366f1; font-size: 14px; text-decoration: none; font-weight: 500;">Contact Support</a>
            </td>
        </tr>
    </table>
</div>
@endsection
