@extends('emails.layouts.base')

@section('title', 'New Sale!')

@section('preview')
You just made a sale! {{ format_price($totalEarnings) }} has been added to your earnings.
@endsection

@section('hero')
<div style="background: #ffffff; padding: 40px; text-align: center;">
    <!-- Success Icon -->
    <div style="width: 96px; height: 96px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 24px; margin: 0 auto 24px; box-shadow: 0 16px 32px -8px rgba(16, 185, 129, 0.4);">
        <table role="presentation" width="100%" height="100%">
            <tr>
                <td align="center" valign="middle">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="#ffffff"/>
                    </svg>
                </td>
            </tr>
        </table>
    </div>
    <h1 style="font-size: 28px; font-weight: 700; color: #0f172a; margin: 0 0 12px; line-height: 1.3;">New Sale!</h1>
    <p style="font-size: 16px; color: #64748b; margin: 0;">Congratulations! You just made a sale.</p>
</div>
@endsection

@section('content')
<div style="padding: 0 40px 40px;">
    <!-- Greeting -->
    <p style="font-size: 16px; color: #334155; margin: 0 0 24px;">
        Hi {{ $seller->seller->business_name ?? $seller->name }},
    </p>

    <!-- Main Text -->
    <p style="font-size: 16px; color: #334155; margin: 0 0 24px;">
        Great news! A customer just purchased your product{{ $items->count() > 1 ? 's' : '' }}.
    </p>

    <!-- Earnings Highlight -->
    <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 16px; padding: 24px; text-align: center; margin: 24px 0;">
        <p style="color: rgba(255,255,255,0.9); font-size: 14px; margin: 0 0 8px; text-transform: uppercase; letter-spacing: 1px;">Your Earnings</p>
        <p style="color: #ffffff; font-size: 36px; font-weight: 700; margin: 0;">{{ format_price($totalEarnings) }}</p>
    </div>

    <!-- Order Details -->
    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; margin: 24px 0;">
        <h3 style="font-size: 16px; font-weight: 600; color: #0f172a; margin: 0 0 16px;">Order Details</h3>

        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                    <span style="color: #64748b; font-size: 14px;">Order Number</span>
                </td>
                <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; text-align: right;">
                    <span style="color: #0f172a; font-weight: 600; font-size: 14px;">#{{ $order->order_number }}</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                    <span style="color: #64748b; font-size: 14px;">Date</span>
                </td>
                <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; text-align: right;">
                    <span style="color: #0f172a; font-weight: 600; font-size: 14px;">{{ $order->created_at->format('M d, Y H:i') }}</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 8px 0;">
                    <span style="color: #64748b; font-size: 14px;">Customer</span>
                </td>
                <td style="padding: 8px 0; text-align: right;">
                    <span style="color: #0f172a; font-weight: 600; font-size: 14px;">{{ $order->user->name }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Items Sold -->
    <div style="margin: 24px 0;">
        <h3 style="font-size: 16px; font-weight: 600; color: #0f172a; margin: 0 0 16px;">Items Sold</h3>

        @foreach($items as $item)
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; margin-bottom: 12px;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td style="vertical-align: top;">
                        <p style="font-weight: 600; color: #0f172a; margin: 0 0 4px; font-size: 15px;">{{ $item->product_name }}</p>
                        <p style="color: #64748b; font-size: 13px; margin: 0;">License: {{ ucfirst($item->license_type) }}</p>
                    </td>
                    <td style="text-align: right; vertical-align: top;">
                        <p style="font-weight: 700; color: #10b981; margin: 0; font-size: 16px;">{{ format_price($item->seller_amount) }}</p>
                        <p style="color: #94a3b8; font-size: 12px; margin: 4px 0 0;">after fees</p>
                    </td>
                </tr>
            </table>
        </div>
        @endforeach
    </div>

    <!-- CTA Button -->
    <div style="text-align: center; margin: 32px 0;">
        <a href="{{ url('/seller/orders') }}" style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 10px; box-shadow: 0 4px 14px -2px rgba(99, 102, 241, 0.4);">
            View Order Details
        </a>
    </div>

    <div style="height: 1px; background: linear-gradient(90deg, transparent 0%, #e2e8f0 50%, transparent 100%); margin: 32px 0;"></div>

    <p style="color: #64748b; font-size: 14px; text-align: center; margin: 0;">
        Your earnings are automatically added to your wallet balance. You can withdraw them at any time from your seller dashboard.
    </p>
</div>
@endsection
