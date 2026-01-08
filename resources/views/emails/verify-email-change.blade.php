@php
    use App\Models\SeoSetting;
    use App\Models\Setting;

    $siteName = SeoSetting::get('site_name', config('app.name', 'Codexse'));
    $organizationLogo = SeoSetting::get('organization_logo');
    $logoUrl = $organizationLogo ? asset('storage/' . $organizationLogo) : asset('images/logo.png');
    $siteUrl = config('app.url');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Verify Your New Email Address</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: #334155;
            margin: 0;
            padding: 0;
            background-color: #f1f5f9;
        }
        .email-wrapper {
            padding: 40px 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background-color: #ffffff;
            padding: 32px 40px;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
        }
        .email-body {
            padding: 40px;
        }
        .email-footer {
            background: #f8fafc;
            padding: 24px 40px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
        }
        h1 {
            color: #0f172a;
            font-size: 24px;
            font-weight: 600;
            margin: 0 0 16px;
        }
        p {
            margin: 0 0 16px;
        }
        .highlight {
            background: #f1f5f9;
            padding: 16px 20px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 14px;
            color: #475569;
            margin: 16px 0;
        }
        .btn {
            display: inline-block;
            padding: 14px 32px;
            background: #6366f1;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 24px 0;
        }
        .btn:hover {
            background: #4f46e5;
        }
        .warning-box {
            background: #fef3c7;
            border: 1px solid #fcd34d;
            border-radius: 8px;
            padding: 16px;
            margin: 24px 0;
        }
        .warning-box p {
            margin: 0;
            color: #92400e;
            font-size: 14px;
        }
        .footer-text {
            color: #94a3b8;
            font-size: 12px;
        }
        .logo {
            max-height: 48px;
            width: auto;
        }
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 16px 12px;
            }
            .email-header,
            .email-body,
            .email-footer {
                padding-left: 24px;
                padding-right: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <a href="{{ $siteUrl }}">
                    <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="logo">
                </a>
            </div>

            <!-- Body -->
            <div class="email-body">
                <h1>Verify Your New Email Address</h1>

                <p>Hello {{ $user->name }},</p>

                <p>We received a request to change your email address on {{ $siteName }}. Your new email address will be:</p>

                <div class="highlight">
                    {{ $newEmail }}
                </div>

                <p>Please click the button below to verify this email address and complete the change:</p>

                <div style="text-align: center;">
                    <a href="{{ $verificationUrl }}" class="btn">Verify Email Address</a>
                </div>

                <div class="warning-box">
                    <p><strong>Security Notice:</strong> If you did not request this email change, please ignore this email. Your email address will remain unchanged and no action is required.</p>
                </div>

                <p>This verification link will expire on <strong>{{ $expiresAt->format('F j, Y \a\t g:i A') }}</strong> ({{ $expiresAt->diffForHumans() }}).</p>

                <p>If you're having trouble clicking the button, copy and paste this URL into your browser:</p>

                <div class="highlight" style="word-break: break-all;">
                    {{ $verificationUrl }}
                </div>
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p class="footer-text">
                    &copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.
                </p>
                <p class="footer-text">
                    This is an automated security notification. Please do not reply to this email.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
