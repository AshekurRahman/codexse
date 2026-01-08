@extends('emails.layouts.base')

@section('title', 'Verify Your Email Address')

@section('preview')
Please verify your email address to complete your {{ config('app.name') }} registration.
@endsection

@section('hero')
<div style="background: #ffffff; padding: 40px; text-align: center;">
    <!-- Verify Icon - Email envelope with gradient background -->
    <div style="width: 96px; height: 96px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border-radius: 24px; margin: 0 auto 24px; box-shadow: 0 16px 32px -8px rgba(99, 102, 241, 0.4);">
        <table role="presentation" width="100%" height="100%">
            <tr>
                <td align="center" valign="middle">
                    <img src="{{ asset('images/email/envelope.webp') }}" alt="Email" style="width: 48px; height: 48px;">
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
                        <td style="width: 32px; vertical-align: top; padding-right: 12px;">
                            <img src="{{ asset('images/email/info-circle.webp') }}" alt="Info" style="width: 28px; height: 28px;">
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
                <!--[if mso]>
                <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ $verificationUrl }}" style="height:54px;v-text-anchor:middle;width:280px;" arcsize="19%" strokecolor="#4f46e5" fillcolor="#6366f1">
                <w:anchorlock/>
                <center style="color:#ffffff;font-family:sans-serif;font-size:16px;font-weight:bold;">Verify Email Address</center>
                </v:roundrect>
                <![endif]-->
                <!--[if !mso]><!-->
                <a href="{{ $verificationUrl }}" style="display: inline-block; padding: 18px 48px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 10px; box-shadow: 0 4px 14px -3px rgba(99, 102, 241, 0.5);">
                    <img src="{{ asset('images/email/check.webp') }}" alt="" style="width: 18px; height: 18px; vertical-align: middle; margin-right: 8px;">
                    Verify Email Address
                </a>
                <!--<![endif]-->
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
                            <img src="{{ asset('images/email/shopping-bag.webp') }}" alt="" style="width: 20px; height: 20px;">
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
                            <img src="{{ asset('images/email/users.webp') }}" alt="" style="width: 20px; height: 20px;">
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
                            <img src="{{ asset('images/email/wallet.webp') }}" alt="" style="width: 20px; height: 20px;">
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
