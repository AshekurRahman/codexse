@extends('emails.layouts.base')

@section('title', 'Seller Application Approved')

@section('preview')
Congratulations! Your seller application for {{ config('app.name') }} has been approved.
@endsection

@section('hero')
<div style="background: #ffffff; padding: 40px; text-align: center;">
    <!-- Success Icon -->
    <div style="width: 96px; height: 96px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 24px; margin: 0 auto 24px; box-shadow: 0 16px 32px -8px rgba(16, 185, 129, 0.4);">
        <table role="presentation" width="100%" height="100%">
            <tr>
                <td align="center" valign="middle">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 6L9 17L4 12" stroke="#ffffff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </td>
            </tr>
        </table>
    </div>
    <h1 style="font-size: 28px; font-weight: 700; color: #0f172a; margin: 0 0 12px; line-height: 1.3;">You're Approved!</h1>
    <p style="font-size: 16px; color: #64748b; margin: 0;">Welcome to the {{ config('app.name') }} seller community</p>
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
        Great news! Your seller application for <strong>{{ $seller->store_name }}</strong> has been approved. You can now start uploading your products and earning money on {{ config('app.name') }}.
    </p>

    <!-- Success Box -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #86efac; border-radius: 12px; margin-bottom: 24px;">
        <tr>
            <td style="padding: 20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="width: 24px; vertical-align: top; padding-right: 12px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 12L11 14L15 10" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </td>
                        <td>
                            <p style="color: #166534; margin: 0; font-size: 14px; line-height: 1.6;">
                                Your seller dashboard is now active! Access it anytime to manage your products, view analytics, and track your earnings.
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
                <a href="{{ route('seller.dashboard') }}" style="display: inline-block; padding: 18px 48px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 10px; box-shadow: 0 4px 14px -3px rgba(16, 185, 129, 0.5);">
                    Go to Seller Dashboard
                </a>
            </td>
        </tr>
    </table>

    <!-- Divider -->
    <div style="height: 1px; background: linear-gradient(90deg, transparent 0%, #e2e8f0 50%, transparent 100%); margin: 0 0 24px;"></div>

    <!-- Getting Started Section -->
    <p style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px;">Get started with these steps:</p>

    <!-- Steps List -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 24px;">
        <tr>
            <td style="padding: 8px 0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="width: 32px; vertical-align: top;">
                            <div style="width: 24px; height: 24px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 50%; text-align: center; line-height: 24px; color: #ffffff; font-size: 12px; font-weight: 600;">1</div>
                        </td>
                        <td style="padding-left: 12px;">
                            <p style="color: #334155; margin: 0; font-size: 14px;"><strong>Complete your store profile</strong> - Add a banner, logo, and detailed description</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="width: 32px; vertical-align: top;">
                            <div style="width: 24px; height: 24px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 50%; text-align: center; line-height: 24px; color: #ffffff; font-size: 12px; font-weight: 600;">2</div>
                        </td>
                        <td style="padding-left: 12px;">
                            <p style="color: #334155; margin: 0; font-size: 14px;"><strong>Upload your first product</strong> - Add screenshots, descriptions, and set your price</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="width: 32px; vertical-align: top;">
                            <div style="width: 24px; height: 24px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 50%; text-align: center; line-height: 24px; color: #ffffff; font-size: 12px; font-weight: 600;">3</div>
                        </td>
                        <td style="padding-left: 12px;">
                            <p style="color: #334155; margin: 0; font-size: 14px;"><strong>Set up your payout method</strong> - Connect your payment account to receive earnings</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="width: 32px; vertical-align: top;">
                            <div style="width: 24px; height: 24px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 50%; text-align: center; line-height: 24px; color: #ffffff; font-size: 12px; font-weight: 600;">4</div>
                        </td>
                        <td style="padding-left: 12px;">
                            <p style="color: #334155; margin: 0; font-size: 14px;"><strong>Promote your products</strong> - Share on social media and start earning</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Divider -->
    <div style="height: 1px; background: linear-gradient(90deg, transparent 0%, #e2e8f0 50%, transparent 100%); margin: 0 0 24px;"></div>

    <!-- Support -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="text-align: center;">
                <p style="color: #64748b; font-size: 14px; margin: 0 0 8px;">Need help getting started?</p>
                <a href="{{ url('/support') }}" style="color: #6366f1; font-size: 14px; text-decoration: none; font-weight: 500;">Contact Support</a>
            </td>
        </tr>
    </table>
</div>
@endsection
