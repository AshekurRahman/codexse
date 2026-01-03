<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Blocked - {{ config('app.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-icon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 16px;
            padding: 48px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        .icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            background: #fee2e2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .icon svg {
            width: 40px;
            height: 40px;
            color: #dc2626;
        }
        h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 16px;
        }
        p {
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .info {
            background: #f3f4f6;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
            font-size: 14px;
            color: #4b5563;
        }
        .info strong {
            display: block;
            margin-bottom: 4px;
            color: #1f2937;
        }
        a {
            display: inline-block;
            padding: 12px 24px;
            background: #4f46e5;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: background 0.2s;
        }
        a:hover {
            background: #4338ca;
        }
        .footer {
            margin-top: 32px;
            font-size: 12px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>
        </div>

        <h1>Access Blocked</h1>

        <p>{{ $message ?? 'Your access to this website has been temporarily blocked due to suspicious activity.' }}</p>

        <div class="info">
            <strong>Why am I seeing this?</strong>
            This could be due to:
            <ul style="text-align: left; margin-top: 8px; padding-left: 20px;">
                <li>Unusual request patterns</li>
                <li>Suspected automated access</li>
                <li>Security policy violations</li>
            </ul>
        </div>

        <p style="font-size: 14px;">If you believe this is an error, please contact our support team.</p>

        <a href="mailto:{{ config('mail.from.address', 'support@example.com') }}">Contact Support</a>

        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
