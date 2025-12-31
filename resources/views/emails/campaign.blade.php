<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $campaign->subject }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .email-content {
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            text-align: center;
            padding: 30px 40px;
        }
        .email-header a {
            text-decoration: none;
        }
        .logo-container {
            display: inline-flex;
            align-items: center;
            gap: 12px;
        }
        .logo-icon {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo-text {
            font-size: 28px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.5px;
        }
        .email-body-wrapper {
            padding: 40px;
        }
        .email-body {
            color: #333;
        }
        .email-body h1, .email-body h2, .email-body h3 {
            color: #1a1a1a;
        }
        .email-body a {
            color: #6366f1;
        }
        .email-body img {
            max-width: 100%;
            height: auto;
        }
        .email-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 12px;
            color: #888;
        }
        .email-footer a {
            color: #6366f1;
            text-decoration: none;
        }
        .email-footer a:hover {
            text-decoration: underline;
        }
        .unsubscribe-link {
            margin-top: 15px;
        }
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 10px;
            }
            .email-body-wrapper {
                padding: 20px;
            }
            .logo-text {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    @if($previewText)
    <div style="display:none;font-size:1px;color:#ffffff;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;">
        {{ $previewText }}
    </div>
    @endif

    <div class="email-wrapper">
        <div class="email-content">
            <div class="email-header">
                <a href="{{ config('app.url') }}">
                    <div class="logo-container">
                        <div class="logo-icon">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" fill="#ffffff"/>
                            </svg>
                        </div>
                        <span class="logo-text">Codexse</span>
                    </div>
                </a>
            </div>

            <div class="email-body-wrapper">
                <div class="email-body">
                    {!! $content !!}
                </div>

                <div class="email-footer">
                    <p>&copy; {{ date('Y') }} Codexse. All rights reserved.</p>
                    <p class="unsubscribe-link">
                        <a href="{{ $unsubscribeUrl }}">Unsubscribe</a> from our newsletter
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Open tracking pixel -->
    @if(isset($trackingPixelUrl))
    <img src="{{ $trackingPixelUrl }}" width="1" height="1" alt="" style="display:none;width:1px;height:1px;border:0;">
    @endif
</body>
</html>
