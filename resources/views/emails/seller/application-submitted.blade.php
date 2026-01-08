@extends('emails.layouts.base')

@section('title', 'Seller Application Received')

@section('preview')
Your seller application for {{ config('app.name') }} has been received and is under review.
@endsection

@section('hero')
<div style="background: #ffffff; padding: 40px; text-align: center;">
    <!-- Store Icon -->
    <div style="width: 96px; height: 96px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border-radius: 24px; margin: 0 auto 24px; box-shadow: 0 16px 32px -8px rgba(99, 102, 241, 0.4);">
        <table role="presentation" width="100%" height="100%">
            <tr>
                <td align="center" valign="middle">
<img src="{{ asset('images/email/store.webp') }}" alt="Store" style="width: 48px; height: 48px;">
                </td>
            </tr>
        </table>
    </div>
    <h1 style="font-size: 28px; font-weight: 700; color: #0f172a; margin: 0 0 12px; line-height: 1.3;">Application Received!</h1>
    <p style="font-size: 16px; color: #64748b; margin: 0;">We're excited to review your seller application</p>
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
        Thank you for applying to become a seller on {{ config('app.name') }}! We've received your application and our team will review it shortly.
    </p>

    <!-- Application Details Card -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 24px;">
        <tr>
            <td style="padding: 24px;">
                <p style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px;">Application Details</p>

                <!-- Store Name -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="color: #64748b; font-size: 14px;">Store Name</td>
                                    <td align="right" style="color: #0f172a; font-weight: 600; font-size: 14px;">{{ $seller->store_name }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <!-- Status -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="color: #64748b; font-size: 14px;">Status</td>
                                    <td align="right">
                                        <span style="display: inline-block; padding: 4px 12px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #92400e; font-size: 12px; font-weight: 600; border-radius: 20px;">Pending Review</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <!-- Submitted Date -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="padding: 12px 0;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="color: #64748b; font-size: 14px;">Submitted</td>
                                    <td align="right" style="color: #0f172a; font-weight: 600; font-size: 14px;">{{ now()->format('M d, Y') }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Info Box -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 1px solid #93c5fd; border-radius: 12px; margin-bottom: 24px;">
        <tr>
            <td style="padding: 20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="width: 24px; vertical-align: top; padding-right: 12px;">
<img src="{{ asset('images/email/info-circle.webp') }}" alt="Info" style="width: 24px; height: 24px;">
                        </td>
                        <td>
                            <p style="color: #1e40af; margin: 0; font-size: 14px; line-height: 1.6;">
                                Our team typically reviews applications within <strong>1-3 business days</strong>. We'll notify you by email once your application has been reviewed.
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
                <a href="{{ route('seller.pending') }}" style="display: inline-block; padding: 18px 48px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 10px; box-shadow: 0 4px 14px -3px rgba(99, 102, 241, 0.5);">
                    Check Application Status
                </a>
            </td>
        </tr>
    </table>

    <!-- Divider -->
    <div style="height: 1px; background: linear-gradient(90deg, transparent 0%, #e2e8f0 50%, transparent 100%); margin: 0 0 24px;"></div>

    <!-- What's Next Section -->
    <p style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px;">What happens next?</p>

    <!-- Steps List -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 24px;">
        <tr>
            <td style="padding: 8px 0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="width: 32px; vertical-align: top;">
                            <div style="width: 24px; height: 24px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border-radius: 50%; text-align: center; line-height: 24px; color: #ffffff; font-size: 12px; font-weight: 600;">1</div>
                        </td>
                        <td style="padding-left: 12px;">
                            <p style="color: #334155; margin: 0; font-size: 14px;"><strong>Application Review</strong> - Our team reviews your store details and portfolio</p>
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
                            <div style="width: 24px; height: 24px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border-radius: 50%; text-align: center; line-height: 24px; color: #ffffff; font-size: 12px; font-weight: 600;">2</div>
                        </td>
                        <td style="padding-left: 12px;">
                            <p style="color: #334155; margin: 0; font-size: 14px;"><strong>Approval Notification</strong> - You'll receive an email with the decision</p>
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
                            <div style="width: 24px; height: 24px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border-radius: 50%; text-align: center; line-height: 24px; color: #ffffff; font-size: 12px; font-weight: 600;">3</div>
                        </td>
                        <td style="padding-left: 12px;">
                            <p style="color: #334155; margin: 0; font-size: 14px;"><strong>Start Selling</strong> - Set up your store and upload your first products</p>
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
                <p style="color: #64748b; font-size: 14px; margin: 0 0 8px;">Have questions about your application?</p>
                <a href="{{ url('/support') }}" style="color: #6366f1; font-size: 14px; text-decoration: none; font-weight: 500;">Contact Support</a>
            </td>
        </tr>
    </table>
</div>
@endsection
