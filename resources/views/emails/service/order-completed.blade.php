@extends('emails.layouts.base')

@section('title', 'Order Completed - ' . $order->order_number)

@section('preview')
Great news! Order #{{ $order->order_number }} has been completed and your earnings are ready.
@endsection

@section('additional_styles')
<style>
    .header-gradient {
        background: linear-gradient(135deg, #10B981 0%, #059669 50%, #047857 100%);
        position: relative;
        overflow: hidden;
    }
    .earnings-card {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: 2px solid #86efac;
        border-radius: 16px;
        padding: 24px;
        text-align: center;
        margin-bottom: 24px;
    }
    .earnings-amount {
        font-size: 42px;
        font-weight: 800;
        color: #047857;
        margin: 0;
    }
    .success-steps {
        background: #f9fafb;
        border-radius: 12px;
        padding: 20px;
    }
    .step-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 16px;
    }
    .step-item:last-child {
        margin-bottom: 0;
    }
    .step-icon {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, #10b981, #059669);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        flex-shrink: 0;
    }
</style>
@endsection

@section('hero')
<div style="background: #ffffff;">
    <div style="text-align: center; padding: 20px 40px 32px;">
        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 20px; margin: 0 auto 24px; box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4);">
            <table role="presentation" width="100%" height="100%">
                <tr>
                    <td align="center" valign="middle">
                        <span style="font-size: 36px;">&#10003;</span>
                    </td>
                </tr>
            </table>
        </div>
        <h1 style="font-size: 28px; font-weight: 700; color: #111827; margin: 0 0 8px;">Order Completed!</h1>
        <p style="font-size: 16px; color: #6b7280; margin: 0;">Congratulations on another successful delivery</p>
    </div>
</div>
@endsection

@section('content')
<div style="padding: 0 40px 40px;">
    <!-- Greeting -->
    <p style="font-size: 16px; color: #374151; margin: 0 0 24px;">
        Hello {{ $order->seller->user->name ?? 'Seller' }},
    </p>
    <p style="font-size: 16px; color: #6b7280; margin: 0 0 24px;">
        Your client has approved the delivery for order <strong>#{{ $order->order_number }}</strong> and the funds have been released to your wallet!
    </p>

    <!-- Earnings Card -->
    <div class="earnings-card">
        <p style="font-size: 14px; color: #059669; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 8px;">Your Earnings</p>
        <p class="earnings-amount">${{ number_format($order->seller_earnings ?? ($order->price * 0.85), 2) }}</p>
        <p style="font-size: 14px; color: #059669; margin: 8px 0 0;">Added to your wallet</p>
    </div>

    <!-- Order Summary -->
    <div style="background: #f9fafb; border-radius: 12px; padding: 20px; margin-bottom: 24px;">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                    <span style="color: #6b7280; font-size: 14px;">Order Number</span>
                </td>
                <td style="padding: 10px 0; text-align: right; border-bottom: 1px solid #e5e7eb;">
                    <span style="color: #111827; font-weight: 600;">#{{ $order->order_number }}</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                    <span style="color: #6b7280; font-size: 14px;">Service</span>
                </td>
                <td style="padding: 10px 0; text-align: right; border-bottom: 1px solid #e5e7eb;">
                    <span style="color: #111827; font-weight: 600;">{{ $order->service->title ?? $order->title }}</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                    <span style="color: #6b7280; font-size: 14px;">Client</span>
                </td>
                <td style="padding: 10px 0; text-align: right; border-bottom: 1px solid #e5e7eb;">
                    <span style="color: #111827; font-weight: 600;">{{ $order->buyer->name }}</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px 0;">
                    <span style="color: #6b7280; font-size: 14px;">Completed</span>
                </td>
                <td style="padding: 10px 0; text-align: right;">
                    <span style="color: #111827; font-weight: 600;">{{ now()->format('M d, Y') }}</span>
                </td>
            </tr>
        </table>
    </div>

    @if(isset($rating) && $rating)
    <!-- Client Review -->
    <div style="background: #fef3c7; border-radius: 12px; padding: 20px; margin-bottom: 24px; border-left: 4px solid #f59e0b;">
        <p style="font-size: 14px; font-weight: 600; color: #92400e; margin: 0 0 12px;">Client Review</p>
        <div style="margin-bottom: 8px;">
            @for($i = 1; $i <= 5; $i++)
                <span style="font-size: 18px; color: {{ $i <= $rating->rating ? '#f59e0b' : '#d1d5db' }};">&#9733;</span>
            @endfor
            <span style="color: #6b7280; margin-left: 8px; font-size: 14px;">{{ $rating->rating }}/5</span>
        </div>
        @if($rating->comment)
        <p style="font-size: 14px; color: #6b7280; margin: 0; font-style: italic;">"{{ $rating->comment }}"</p>
        @endif
    </div>
    @endif

    <!-- Next Steps -->
    <div class="success-steps">
        <p style="font-size: 14px; font-weight: 600; color: #374151; margin: 0 0 16px;">Keep the momentum going!</p>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="padding: 8px 0; vertical-align: top; width: 30px;">
                    <span style="color: #10b981; font-size: 16px;">&#10003;</span>
                </td>
                <td style="padding: 8px 0; vertical-align: top;">
                    <span style="color: #6b7280; font-size: 14px;">Request a payout to withdraw your earnings</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 8px 0; vertical-align: top; width: 30px;">
                    <span style="color: #10b981; font-size: 16px;">&#10003;</span>
                </td>
                <td style="padding: 8px 0; vertical-align: top;">
                    <span style="color: #6b7280; font-size: 14px;">Ask your client for a testimonial</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 8px 0; vertical-align: top; width: 30px;">
                    <span style="color: #10b981; font-size: 16px;">&#10003;</span>
                </td>
                <td style="padding: 8px 0; vertical-align: top;">
                    <span style="color: #6b7280; font-size: 14px;">Update your portfolio with this project</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- CTA Button -->
    <div style="text-align: center; margin-top: 32px;">
        <a href="{{ url('/wallet') }}" style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 30px; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);">
            View Wallet
        </a>
    </div>
</div>
@endsection
