@php
    use App\Models\SeoSetting;
    use App\Models\Setting;

    $siteName = SeoSetting::get('site_name', config('app.name', 'Codexse'));
    $organizationLogo = SeoSetting::get('organization_logo');

    // Determine logo URL based on path format
    if ($organizationLogo) {
        // If path starts with / or images/, it's in public folder
        if (str_starts_with($organizationLogo, '/') || str_starts_with($organizationLogo, 'images/')) {
            $logoUrl = asset(ltrim($organizationLogo, '/'));
        } else {
            $logoUrl = asset('storage/' . $organizationLogo);
        }
    } else {
        $logoUrl = asset('images/logo.svg');
    }

    $siteUrl = config('app.url');
    $supportEmail = Setting::get('support_email', 'support@codexse.com');
    $companyAddress = Setting::get('company_address', '');
@endphp
<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="format-detection" content="telephone=no,address=no,email=no,date=no,url=no">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>@yield('title', $siteName)</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <style>
        table {border-collapse: collapse;}
        td,th,div,p,a,h1,h2,h3,h4,h5,h6 {font-family: "Segoe UI", sans-serif; mso-line-height-rule: exactly;}
    </style>
    <![endif]-->
    <style>
        /* Reset & Base */
        :root {
            color-scheme: light;
            supported-color-schemes: light;
        }
        * { box-sizing: border-box; }
        body, table, td, p, a, li, blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }
        table { border-collapse: collapse !important; }
        body {
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            background-color: #f1f5f9;
        }

        /* Typography */
        body, td, p {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: #334155;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #0f172a;
            margin: 0;
            padding: 0;
            font-weight: 600;
        }
        a { color: #6366f1; text-decoration: none; }
        a:hover { text-decoration: underline; }

        /* Container */
        .email-wrapper {
            width: 100%;
            background-color: #f1f5f9;
            padding: 40px 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
        }

        /* Header */
        .email-header {
            background-color: #ffffff;
            padding: 32px 40px;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
        }
        .email-header img {
            max-height: 48px;
            width: auto;
        }

        /* Hero Section */
        .email-hero {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            padding: 48px 40px;
            text-align: center;
        }
        .email-hero-light {
            background-color: #ffffff;
            padding: 40px;
            text-align: center;
        }
        .hero-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            margin: 0 auto 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hero-title {
            font-size: 28px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 12px;
            line-height: 1.3;
        }
        .hero-subtitle {
            font-size: 16px;
            color: #64748b;
            margin: 0;
        }

        /* Content */
        .email-content {
            padding: 40px;
        }
        .email-content p {
            margin: 0 0 16px;
        }
        .email-content p:last-child {
            margin-bottom: 0;
        }

        /* Info Card */
        .info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            margin: 24px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .info-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .info-row:first-child {
            padding-top: 0;
        }
        .info-label {
            color: #64748b;
            font-size: 14px;
        }
        .info-value {
            color: #0f172a;
            font-weight: 600;
            font-size: 14px;
        }

        /* Buttons */
        .btn-wrapper {
            text-align: center;
            margin: 32px 0;
        }
        .btn {
            display: inline-block;
            padding: 16px 32px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none !important;
            text-align: center;
            transition: all 0.2s;
            mso-padding-alt: 0;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: #ffffff !important;
        }
        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff !important;
        }
        .btn-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #ffffff !important;
        }
        .btn-secondary {
            background: #ffffff;
            color: #334155 !important;
            border: 2px solid #e2e8f0;
        }

        /* Alert Boxes */
        .alert {
            border-radius: 12px;
            padding: 20px;
            margin: 24px 0;
        }
        .alert-info {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 1px solid #93c5fd;
        }
        .alert-success {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 1px solid #86efac;
        }
        .alert-warning {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border: 1px solid #fcd34d;
        }
        .alert-danger {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 1px solid #fca5a5;
        }

        /* Divider */
        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, #e2e8f0 50%, transparent 100%);
            margin: 32px 0;
        }

        /* Footer */
        .email-footer {
            background: #f8fafc;
            padding: 40px;
            border-top: 1px solid #e2e8f0;
        }
        .footer-logo {
            text-align: center;
            margin-bottom: 24px;
        }
        .footer-logo img {
            max-height: 36px;
            width: auto;
            opacity: 0.8;
        }
        .footer-links {
            text-align: center;
            margin-bottom: 24px;
        }
        .footer-links a {
            color: #64748b;
            text-decoration: none;
            font-size: 14px;
            margin: 0 12px;
        }
        .footer-links a:hover {
            color: #6366f1;
        }
        .footer-social {
            text-align: center;
            margin-bottom: 24px;
        }
        .footer-social a {
            display: inline-block;
            width: 36px;
            height: 36px;
            background: #e2e8f0;
            border-radius: 50%;
            margin: 0 6px;
            line-height: 36px;
            text-align: center;
        }
        .footer-divider {
            height: 1px;
            background: #e2e8f0;
            margin: 24px 0;
        }
        .footer-legal {
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.8;
        }
        .footer-legal a {
            color: #64748b;
        }
        .footer-legal p {
            margin: 0 0 8px;
        }

        /* Post Footer */
        .post-footer {
            text-align: center;
            padding: 24px 0;
            color: #94a3b8;
            font-size: 12px;
        }
        .post-footer a {
            color: #6366f1;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 16px 12px !important;
            }
            .email-container {
                border-radius: 12px !important;
            }
            .email-header,
            .email-hero,
            .email-hero-light,
            .email-content,
            .email-footer {
                padding-left: 24px !important;
                padding-right: 24px !important;
            }
            .hero-title {
                font-size: 24px !important;
            }
            .btn {
                display: block !important;
                width: 100% !important;
            }
            .footer-links a {
                display: block;
                margin: 8px 0;
            }
        }
    </style>
    @yield('additional_styles')
</head>
<body>
    <!-- Preview Text -->
    @hasSection('preview')
    <div style="display:none;font-size:1px;color:#f1f5f9;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;">
        @yield('preview')
        &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847;
    </div>
    @endif

    <div class="email-wrapper" style="background-color: #f1f5f9; padding: 40px 20px;">
        <!--[if mso]>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" align="center">
        <tr>
        <td>
        <![endif]-->

        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="max-width: 600px; margin: 0 auto;">
            <tr>
                <td>
                    <div class="email-container" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">

                        <!-- Header -->
                        <div class="email-header" style="background-color: #ffffff; padding: 32px 40px; text-align: center; border-bottom: 1px solid #e2e8f0;">
                            <a href="{{ $siteUrl }}" style="text-decoration: none;">
                                <span style="font-size: 28px; font-weight: 700; color: #6366f1;">{{ $siteName }}</span>
                            </a>
                        </div>

                        <!-- Hero Section -->
                        @yield('hero')

                        <!-- Content Section -->
                        @yield('content')

                        <!-- Footer -->
                        <div class="email-footer" style="background: #f8fafc; padding: 40px; border-top: 1px solid #e2e8f0;">
                            <!-- Footer Links -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center" style="padding-bottom: 24px;">
                                        <a href="{{ url('/products') }}" style="color: #64748b; text-decoration: none; font-size: 14px; margin: 0 12px;">Products</a>
                                        <a href="{{ url('/services') }}" style="color: #64748b; text-decoration: none; font-size: 14px; margin: 0 12px;">Services</a>
                                        <a href="{{ url('/support') }}" style="color: #64748b; text-decoration: none; font-size: 14px; margin: 0 12px;">Support</a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Divider -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="padding-bottom: 24px;">
                                        <div style="height: 1px; background: #e2e8f0;"></div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Legal Links -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center" style="padding-bottom: 16px;">
                                        <a href="{{ url('/privacy-policy') }}" style="color: #64748b; text-decoration: none; font-size: 12px; margin: 0 8px;">Privacy Policy</a>
                                        <span style="color: #cbd5e1;">|</span>
                                        <a href="{{ url('/terms') }}" style="color: #64748b; text-decoration: none; font-size: 12px; margin: 0 8px;">Terms of Service</a>
                                        <span style="color: #cbd5e1;">|</span>
                                        <a href="{{ url('/contact') }}" style="color: #64748b; text-decoration: none; font-size: 12px; margin: 0 8px;">Contact Us</a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Company Info -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <p style="color: #94a3b8; font-size: 12px; margin: 0 0 8px; line-height: 1.6;">
                                            &copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.
                                        </p>
                                        @if($companyAddress)
                                        <p style="color: #94a3b8; font-size: 12px; margin: 0; line-height: 1.6;">
                                            {{ $companyAddress }}
                                        </p>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>

                    </div>

                    <!-- Post Footer -->
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                        <tr>
                            <td align="center" style="padding: 24px 0;">
                                <p style="color: #94a3b8; font-size: 12px; margin: 0 0 8px;">
                                    This email was sent to {{ $recipientEmail ?? 'you' }}
                                </p>
                                @hasSection('unsubscribe')
                                <p style="color: #94a3b8; font-size: 12px; margin: 0;">
                                    @yield('unsubscribe')
                                </p>
                                @endif
                            </td>
                        </tr>
                    </table>

                </td>
            </tr>
        </table>

        <!--[if mso]>
        </td>
        </tr>
        </table>
        <![endif]-->
    </div>
</body>
</html>
