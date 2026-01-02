@extends('emails.layouts.base')

@section('title', 'Seller Application Update')

@section('preview')
Update on your seller application for {{ config('app.name') }}.
@endsection

@section('hero')
<div style="background: #ffffff; padding: 40px; text-align: center;">
    <!-- Info Icon -->
    <div style="width: 96px; height: 96px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border-radius: 24px; margin: 0 auto 24px; box-shadow: 0 16px 32px -8px rgba(99, 102, 241, 0.4);">
        <table role="presentation" width="100%" height="100%">
            <tr>
                <td align="center" valign="middle">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 16V12M12 8H12.01" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </td>
            </tr>
        </table>
    </div>
    <h1 style="font-size: 28px; font-weight: 700; color: #0f172a; margin: 0 0 12px; line-height: 1.3;">Application Update</h1>
    <p style="font-size: 16px; color: #64748b; margin: 0;">We've reviewed your seller application</p>
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
        Thank you for your interest in becoming a seller on {{ config('app.name') }}. After reviewing your application for <strong>{{ $seller->store_name }}</strong>, we regret to inform you that we are unable to approve it at this time.
    </p>

    @if($reason)
    <!-- Reason Box -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 24px;">
        <tr>
            <td style="padding: 24px;">
                <p style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 12px;">Reason for Decision</p>
                <p style="color: #334155; font-size: 14px; margin: 0; line-height: 1.6;">{{ $reason }}</p>
            </td>
        </tr>
    </table>
    @endif

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
                                <strong>You can reapply!</strong> After addressing the feedback above, you're welcome to submit a new application. We'd love to have you in our seller community.
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
                <a href="{{ route('seller.apply') }}" style="display: inline-block; padding: 18px 48px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 10px; box-shadow: 0 4px 14px -3px rgba(99, 102, 241, 0.5);">
                    Submit New Application
                </a>
            </td>
        </tr>
    </table>

    <!-- Divider -->
    <div style="height: 1px; background: linear-gradient(90deg, transparent 0%, #e2e8f0 50%, transparent 100%); margin: 0 0 24px;"></div>

    <!-- Tips Section -->
    <p style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px;">Tips for a successful application:</p>

    <!-- Tips List -->
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
                            <p style="color: #334155; margin: 0; font-size: 14px;">Provide a detailed store description that highlights your expertise</p>
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
                            <p style="color: #334155; margin: 0; font-size: 14px;">Include a link to your portfolio or previous work</p>
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
                            <p style="color: #334155; margin: 0; font-size: 14px;">Use a professional store name that reflects your brand</p>
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
                <p style="color: #64748b; font-size: 14px; margin: 0 0 8px;">Have questions about our requirements?</p>
                <a href="{{ url('/support') }}" style="color: #6366f1; font-size: 14px; text-decoration: none; font-weight: 500;">Contact Support</a>
            </td>
        </tr>
    </table>
</div>
@endsection
