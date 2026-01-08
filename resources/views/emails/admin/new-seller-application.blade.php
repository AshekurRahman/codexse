@extends('emails.layouts.base')

@section('title', 'New Seller Application')

@section('preview')
New seller application from {{ $applicant->name }} - {{ $seller->store_name }}
@endsection

@section('hero')
<div style="background: #ffffff; padding: 40px; text-align: center;">
    <!-- Alert Icon -->
    <div style="width: 96px; height: 96px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 24px; margin: 0 auto 24px; box-shadow: 0 16px 32px -8px rgba(245, 158, 11, 0.4);">
        <table role="presentation" width="100%" height="100%">
            <tr>
                <td align="center" valign="middle">
<img src="{{ asset('images/email/user-plus.webp') }}" alt="New Application" style="width: 48px; height: 48px;">
                </td>
            </tr>
        </table>
    </div>
    <h1 style="font-size: 28px; font-weight: 700; color: #0f172a; margin: 0 0 12px; line-height: 1.3;">New Seller Application</h1>
    <p style="font-size: 16px; color: #64748b; margin: 0;">A new seller application requires your review</p>
</div>
@endsection

@section('content')
<div style="padding: 0 40px 40px;">
    <!-- Greeting -->
    <p style="font-size: 16px; color: #334155; margin: 0 0 24px;">
        Hi {{ $admin->name ?? 'Admin' }},
    </p>

    <!-- Main Text -->
    <p style="font-size: 16px; color: #334155; margin: 0 0 24px;">
        A new seller application has been submitted and is awaiting your review.
    </p>

    <!-- Applicant Details Card -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 24px;">
        <tr>
            <td style="padding: 24px;">
                <p style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px;">Applicant Details</p>

                <!-- Name -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="color: #64748b; font-size: 14px;">Applicant Name</td>
                                    <td align="right" style="color: #0f172a; font-weight: 600; font-size: 14px;">{{ $applicant->name }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <!-- Email -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="color: #64748b; font-size: 14px;">Email</td>
                                    <td align="right" style="color: #0f172a; font-weight: 600; font-size: 14px;">{{ $applicant->email }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

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

                <!-- Submitted Date -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="padding: 12px 0;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="color: #64748b; font-size: 14px;">Submitted</td>
                                    <td align="right" style="color: #0f172a; font-weight: 600; font-size: 14px;">{{ now()->format('M d, Y \a\t h:i A') }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Store Description -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 24px;">
        <tr>
            <td style="padding: 24px;">
                <p style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 12px;">Store Description</p>
                <p style="color: #334155; font-size: 14px; margin: 0; line-height: 1.6;">{{ $seller->description }}</p>
            </td>
        </tr>
    </table>

    <!-- CTA Button -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td align="center" style="padding: 16px 0 32px;">
                <a href="{{ url('/admin/sellers/' . $seller->id) }}" style="display: inline-block; padding: 18px 48px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 10px; box-shadow: 0 4px 14px -3px rgba(245, 158, 11, 0.5);">
                    Review Application
                </a>
            </td>
        </tr>
    </table>

    <!-- Divider -->
    <div style="height: 1px; background: linear-gradient(90deg, transparent 0%, #e2e8f0 50%, transparent 100%); margin: 0 0 24px;"></div>

    <!-- Footer Note -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="text-align: center;">
                <p style="color: #64748b; font-size: 14px; margin: 0;">
                    You can approve or reject this application from the admin panel.
                </p>
            </td>
        </tr>
    </table>
</div>
@endsection
