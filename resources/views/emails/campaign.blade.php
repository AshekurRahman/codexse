@php
    use App\Models\SeoSetting;
    use App\Models\Setting;

    $siteName = SeoSetting::get('site_name', config('app.name', 'Codexse'));
    $organizationLogo = SeoSetting::get('organization_logo');
    $logoUrl = $organizationLogo ? asset('storage/' . $organizationLogo) : asset('images/logo.png');
    $siteUrl = config('app.url');
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
    <title>{{ $campaign->subject }}</title>
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
            margin: 0 0 16px;
            padding: 0;
            font-weight: 600;
        }
        h1 { font-size: 28px; }
        h2 { font-size: 24px; }
        h3 { font-size: 20px; }
        a { color: #6366f1; text-decoration: none; }
        a:hover { text-decoration: underline; }
        p { margin: 0 0 16px; }

        /* Content Styling */
        .email-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .email-content ul, .email-content ol {
            margin: 0 0 16px;
            padding-left: 24px;
        }
        .email-content li {
            margin-bottom: 8px;
        }
        .email-content blockquote {
            margin: 16px 0;
            padding: 16px 20px;
            background: #f8fafc;
            border-left: 4px solid #6366f1;
            border-radius: 0 8px 8px 0;
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
            .email-body,
            .email-footer {
                padding-left: 24px !important;
                padding-right: 24px !important;
            }
            h1 { font-size: 24px !important; }
        }
    </style>
</head>
<body>
    <!-- Preview Text -->
    @if($previewText)
    <div style="display:none;font-size:1px;color:#f1f5f9;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;">
        {{ $previewText }}
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
                            <a href="{{ $siteUrl }}" style="display: inline-block;">
                                <img src="{{ $logoUrl }}" alt="{{ $siteName }}" style="max-height: 48px; width: auto; display: block;">
                            </a>
                        </div>

                        <!-- Content -->
                        <div class="email-body" style="padding: 40px;">
                            <div class="email-content">
                                {!! $content !!}
                            </div>
                        </div>

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

                            <!-- Social Links -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center" style="padding-bottom: 24px;">
                                        @php
                                            $facebook = Setting::get('social_facebook');
                                            $twitter = Setting::get('social_twitter');
                                            $instagram = Setting::get('social_instagram');
                                            $linkedin = Setting::get('social_linkedin');
                                        @endphp
                                        @if($facebook)
                                        <a href="{{ $facebook }}" style="display: inline-block; width: 36px; height: 36px; background: #e2e8f0; border-radius: 50%; margin: 0 4px; text-align: center; line-height: 36px;">
                                            <img src="{{ asset('images/icons/facebook.webp') }}" alt="Facebook" width="18" height="18" style="vertical-align: middle;">
                                        </a>
                                        @endif
                                        @if($twitter)
                                        <a href="{{ $twitter }}" style="display: inline-block; width: 36px; height: 36px; background: #e2e8f0; border-radius: 50%; margin: 0 4px; text-align: center; line-height: 36px;">
                                            <img src="{{ asset('images/icons/twitter.webp') }}" alt="Twitter" width="18" height="18" style="vertical-align: middle;">
                                        </a>
                                        @endif
                                        @if($instagram)
                                        <a href="{{ $instagram }}" style="display: inline-block; width: 36px; height: 36px; background: #e2e8f0; border-radius: 50%; margin: 0 4px; text-align: center; line-height: 36px;">
                                            <img src="{{ asset('images/icons/instagram.webp') }}" alt="Instagram" width="18" height="18" style="vertical-align: middle;">
                                        </a>
                                        @endif
                                        @if($linkedin)
                                        <a href="{{ $linkedin }}" style="display: inline-block; width: 36px; height: 36px; background: #e2e8f0; border-radius: 50%; margin: 0 4px; text-align: center; line-height: 36px;">
                                            <img src="{{ asset('images/icons/linkedin.webp') }}" alt="LinkedIn" width="18" height="18" style="vertical-align: middle;">
                                        </a>
                                        @endif
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
                                        <p style="color: #94a3b8; font-size: 12px; margin: 0 0 8px; line-height: 1.6;">
                                            {{ $companyAddress }}
                                        </p>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>

                    </div>

                    <!-- Post Footer with Unsubscribe -->
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                        <tr>
                            <td align="center" style="padding: 24px 0;">
                                <p style="color: #94a3b8; font-size: 12px; margin: 0 0 8px;">
                                    You received this email because you subscribed to our newsletter.
                                </p>
                                <p style="color: #94a3b8; font-size: 12px; margin: 0;">
                                    <a href="{{ $unsubscribeUrl }}" style="color: #6366f1; text-decoration: none;">Unsubscribe</a> from future emails
                                </p>
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

    <!-- Open tracking pixel -->
    @if(isset($trackingPixelUrl))
    <img src="{{ $trackingPixelUrl }}" width="1" height="1" alt="" style="display:none;width:1px;height:1px;border:0;">
    @endif
</body>
</html>
