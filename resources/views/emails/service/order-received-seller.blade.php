@extends('emails.layouts.base')

@section('title', 'New Order Received - ' . $order->order_number)

@section('preview')
You have a new service order! #{{ $order->order_number }} - {{ format_price($order->seller_amount) }}
@endsection

@section('additional_styles')
<style>
    .header-gradient {
        background: linear-gradient(135deg, #059669 0%, #10b981 50%, #34d399 100%);
        position: relative;
        overflow: hidden;
    }
    .header-gradient::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.08'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .earnings-card {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        border: 2px solid #10b981;
        border-radius: 16px;
        padding: 24px;
        text-align: center;
        margin-bottom: 24px;
    }
    .earnings-label {
        font-size: 12px;
        color: #059669;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 600;
        margin: 0 0 8px;
    }
    .earnings-amount {
        font-size: 36px;
        font-weight: 800;
        color: #047857;
        margin: 0;
    }
    .buyer-card {
        background: #f9fafb;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        gap: 16px;
        align-items: center;
        margin-bottom: 24px;
    }
    .buyer-avatar {
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 20px;
        font-weight: 700;
    }
    .requirements-box {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 12px;
        padding: 20px;
        margin: 24px 0;
    }
    .action-buttons {
        display: flex;
        gap: 12px;
        margin: 24px 0;
    }
</style>
@endsection

@section('hero')
<div style="background: #ffffff;">
    <div style="text-align: center; padding: 20px 40px 32px;">
        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); border-radius: 20px; margin: 0 auto 24px; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px -5px rgba(251, 191, 36, 0.4);">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.709 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4881 2.02168 11.3363C2.16356 9.18457 2.99721 7.13633 4.39828 5.49707C5.79935 3.85782 7.69279 2.71538 9.79619 2.24015C11.8996 1.76491 14.1003 1.98234 16.07 2.86" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M22 4L12 14.01L9 11.01" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h1 style="font-size: 28px; font-weight: 700; color: #111827; margin: 0 0 8px;">New Order Received!</h1>
        <p style="font-size: 16px; color: #6b7280; margin: 0;">Time to deliver amazing work</p>
    </div>
</div>
@endsection

@section('content')
<div class="email-content">
    <!-- Earnings Card -->
    <div class="earnings-card">
        <p class="earnings-label">Your Earnings</p>
        <p class="earnings-amount">{{ format_price($order->seller_amount) }}</p>
    </div>

    <!-- Buyer Info -->
    <div class="buyer-card">
        <div class="buyer-avatar">
            {{ strtoupper(substr($order->buyer->name, 0, 1)) }}
        </div>
        <div>
            <p style="font-weight: 600; color: #111827; margin: 0; font-size: 16px;">{{ $order->buyer->name }}</p>
            <p style="color: #6b7280; margin: 4px 0 0; font-size: 14px;">{{ $order->buyer->email }}</p>
        </div>
    </div>

    <!-- Order Details -->
    <div class="info-card">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                    <span style="color: #6b7280; font-size: 14px;">Order Number</span>
                </td>
                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; text-align: right;">
                    <span style="color: #111827; font-weight: 600; font-size: 14px;">#{{ $order->order_number }}</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                    <span style="color: #6b7280; font-size: 14px;">Service</span>
                </td>
                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; text-align: right;">
                    <span style="color: #111827; font-weight: 600; font-size: 14px;">{{ $order->title }}</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                    <span style="color: #6b7280; font-size: 14px;">Package</span>
                </td>
                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; text-align: right;">
                    <span style="color: #111827; font-weight: 600; font-size: 14px;">{{ $order->package_name ?? 'Standard' }}</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                    <span style="color: #6b7280; font-size: 14px;">Delivery Deadline</span>
                </td>
                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; text-align: right;">
                    <span style="color: #dc2626; font-weight: 600; font-size: 14px;">{{ now()->addDays($order->delivery_days ?? 3)->format('F j, Y') }}</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 12px 0;">
                    <span style="color: #6b7280; font-size: 14px;">Revisions Included</span>
                </td>
                <td style="padding: 12px 0; text-align: right;">
                    <span style="color: #111827; font-weight: 600; font-size: 14px;">{{ $order->revisions ?? 'Unlimited' }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Requirements -->
    @if($order->requirements)
    <div class="requirements-box">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="vertical-align: top; padding-right: 12px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 5H7C5.89543 5 5 5.89543 5 7V19C5 20.1046 5.89543 21 7 21H17C18.1046 21 19 20.1046 19 19V7C19 5.89543 18.1046 5 17 5H15M9 5C9 6.10457 9.89543 7 11 7H13C14.1046 7 15 6.10457 15 5M9 5C9 3.89543 9.89543 3 11 3H13C14.1046 3 15 3.89543 15 5" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </td>
                <td>
                    <p style="font-weight: 600; color: #92400e; margin: 0 0 8px; font-size: 14px;">Buyer Requirements</p>
                    <p style="color: #a16207; margin: 0; font-size: 14px; white-space: pre-line;">{{ $order->requirements }}</p>
                </td>
            </tr>
        </table>
    </div>
    @endif

    <!-- Action Buttons -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td align="center" style="padding: 24px 0;">
                <a href="{{ url('/seller/service-orders/' . $order->id) }}" style="display: inline-block; padding: 16px 32px; background: linear-gradient(135deg, #059669 0%, #047857 100%); color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 12px; box-shadow: 0 4px 14px -3px rgba(5, 150, 105, 0.5); margin-right: 12px;">
                    Start Working
                </a>
                <a href="{{ url('/seller/service-orders/' . $order->id . '#messages') }}" style="display: inline-block; padding: 16px 24px; background: #ffffff; color: #374151; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 12px; border: 2px solid #e5e7eb;">
                    Message Buyer
                </a>
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <!-- Tips -->
    <div style="background: #f0fdf4; border-radius: 12px; padding: 20px;">
        <h3 style="font-size: 14px; font-weight: 600; color: #166534; margin: 0 0 12px;">Tips for Success</h3>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="padding: 6px 0; vertical-align: top;">
                    <span style="color: #22c55e; margin-right: 8px;">&#10003;</span>
                    <span style="color: #166534; font-size: 13px;">Respond to the buyer within 24 hours</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 6px 0; vertical-align: top;">
                    <span style="color: #22c55e; margin-right: 8px;">&#10003;</span>
                    <span style="color: #166534; font-size: 13px;">Ask clarifying questions before starting</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 6px 0; vertical-align: top;">
                    <span style="color: #22c55e; margin-right: 8px;">&#10003;</span>
                    <span style="color: #166534; font-size: 13px;">Deliver before the deadline for great reviews</span>
                </td>
            </tr>
        </table>
    </div>
</div>
@endsection
