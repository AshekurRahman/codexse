<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Confirm Your Newsletter Subscription</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 30px; border-radius: 10px;">
        <h1 style="color: #2d3748; margin-bottom: 20px;">Confirm Your Subscription</h1>

        <p>Hello,</p>

        <p>Thank you for subscribing to our newsletter! To complete your subscription and start receiving updates, please confirm your email address by clicking the button below:</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $confirmUrl }}"
               style="background-color: #4f46e5; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                Confirm Subscription
            </a>
        </div>

        <p>Or copy and paste this link into your browser:</p>
        <p style="word-break: break-all; color: #4f46e5;">{{ $confirmUrl }}</p>

        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 30px 0;">

        <p style="color: #718096; font-size: 14px;">
            If you didn't subscribe to our newsletter, you can safely ignore this email.
        </p>

        <p style="color: #718096; font-size: 14px;">
            This link will expire in 24 hours.
        </p>
    </div>
</body>
</html>
