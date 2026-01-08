@extends('emails.layouts.base')

@section('title', 'Service Order Confirmed - ' . $order->order_number)

@section('preview')
Your service order #{{ $order->order_number }} has been placed successfully!
@endsection

@section('additional_styles')
<style>
    .service-card {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border: 1px solid #bae6fd;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
    }
    .service-title {
        font-size: 18px;
        font-weight: 700;
        color: #0c4a6e;
        margin: 0 0 8px;
    }
    .service-seller {
        font-size: 14px;
        color: #0369a1;
        margin: 0 0 16px;
    }
    .package-badge {
        display: inline-block;
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        color: #ffffff;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }
</style>
@endsection

@section('hero')
<div style="background: #ffffff;">
    <div style="text-align: center; padding: 20px 40px 32px;">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="80" height="80" style="margin: 0 auto 24px; background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); border-radius: 20px; box-shadow: 0 10px 25px -5px rgba(14, 165, 233, 0.4);">
            <tr>
                <td align="center" valign="middle">
                    <img src="{{ asset('images/icons/service.webp') }}" alt="Service" width="40" height="40" style="display: block;">
                </td>
            </tr>
        </table>
        <h1 style="font-size: 28px; font-weight: 700; color: #111827; margin: 0 0 8px;">Service Order Placed!</h1>
        <p style="font-size: 16px; color: #6b7280; margin: 0;">Your freelancer will start working on it soon</p>
    </div>
</div>
@endsection

@section('content')
<div class="email-content">
    <!-- Service Details -->
    <div class="service-card">
        <h2 class="service-title">{{ $order->title }}</h2>
        <p class="service-seller">by {{ $order->seller->business_name ?? $order->seller->user->name }}</p>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td>
                    <span class="package-badge">{{ $order->package_name ?? 'Standard' }} Package</span>
                </td>
                <td style="text-align: right;">
                    <span style="font-size: 24px; font-weight: 700; color: #0c4a6e;">{{ format_price($order->price) }}</span>
                </td>
            </tr>
        </table>
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
                    <span style="color: #6b7280; font-size: 14px;">Delivery Time</span>
                </td>
                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; text-align: right;">
                    <span style="color: #111827; font-weight: 600; font-size: 14px;">{{ $order->delivery_days ?? 3 }} days</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                    <span style="color: #6b7280; font-size: 14px;">Expected Delivery</span>
                </td>
                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; text-align: right;">
                    <span style="color: #111827; font-weight: 600; font-size: 14px;">{{ now()->addDays($order->delivery_days ?? 3)->format('F j, Y') }}</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 12px 0;">
                    <span style="color: #6b7280; font-size: 14px;">Revisions</span>
                </td>
                <td style="padding: 12px 0; text-align: right;">
                    <span style="color: #111827; font-weight: 600; font-size: 14px;">{{ $order->revisions ?? 'Unlimited' }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Order Timeline -->
    <div style="background: #f9fafb; border-radius: 12px; padding: 20px; margin: 24px 0;">
        <h3 style="font-size: 14px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px;">Order Progress</h3>

        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="padding: 12px 0; vertical-align: top; width: 28px;">
                    <div style="width: 12px; height: 12px; border-radius: 50%; background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2);"></div>
                </td>
                <td style="padding: 12px 0; vertical-align: top;">
                    <p style="font-weight: 600; color: #111827; margin: 0; font-size: 14px;">Order Placed</p>
                    <p style="color: #6b7280; margin: 4px 0 0; font-size: 13px;">{{ $order->created_at->format('M j, Y g:i A') }}</p>
                </td>
            </tr>
            <tr>
                <td style="padding: 12px 0; vertical-align: top; width: 28px;">
                    <div style="width: 12px; height: 12px; border-radius: 50%; background: #d1d5db;"></div>
                </td>
                <td style="padding: 12px 0; vertical-align: top;">
                    <p style="font-weight: 600; color: #6b7280; margin: 0; font-size: 14px;">In Progress</p>
                    <p style="color: #9ca3af; margin: 4px 0 0; font-size: 13px;">Seller will start working</p>
                </td>
            </tr>
            <tr>
                <td style="padding: 12px 0; vertical-align: top; width: 28px;">
                    <div style="width: 12px; height: 12px; border-radius: 50%; background: #d1d5db;"></div>
                </td>
                <td style="padding: 12px 0; vertical-align: top;">
                    <p style="font-weight: 600; color: #6b7280; margin: 0; font-size: 14px;">Delivered</p>
                    <p style="color: #9ca3af; margin: 4px 0 0; font-size: 13px;">You'll receive your files</p>
                </td>
            </tr>
            <tr>
                <td style="padding: 12px 0 0; vertical-align: top; width: 28px;">
                    <div style="width: 12px; height: 12px; border-radius: 50%; background: #d1d5db;"></div>
                </td>
                <td style="padding: 12px 0 0; vertical-align: top;">
                    <p style="font-weight: 600; color: #6b7280; margin: 0; font-size: 14px;">Completed</p>
                    <p style="color: #9ca3af; margin: 4px 0 0; font-size: 13px;">Order marked as complete</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Escrow Notice -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: linear-gradient(135deg, #fefce8 0%, #fef9c3 100%); border: 1px solid #fde047; border-radius: 12px;">
        <tr>
            <td style="padding: 20px; width: 56px; vertical-align: top;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="40" height="40" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); border-radius: 10px;">
                    <tr>
                        <td align="center" valign="middle">
                            <span style="font-size: 18px;">&#128274;</span>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="padding: 20px 20px 20px 0; vertical-align: middle;">
                <p style="font-weight: 600; color: #854d0e; margin: 0 0 4px; font-size: 14px;">Payment Secured in Escrow</p>
                <p style="color: #a16207; margin: 0; font-size: 13px;">Your payment is held securely until you approve the delivery. You're protected!</p>
            </td>
        </tr>
    </table>

    <!-- CTA Button -->
    <div style="text-align: center; margin: 32px 0;">
        <a href="{{ url('/service-orders/' . $order->id) }}" style="display: inline-block; padding: 16px 32px; background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 12px; box-shadow: 0 4px 14px -3px rgba(14, 165, 233, 0.5);">
            View Order Details
        </a>
    </div>

    <div class="divider"></div>

    <!-- Communication -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="text-align: center;">
                <p style="color: #6b7280; font-size: 14px; margin: 0 0 8px;">Have questions for the seller?</p>
                <a href="{{ url('/service-orders/' . $order->id . '#messages') }}" style="color: #0ea5e9; font-size: 14px; text-decoration: none; font-weight: 500;">Send a Message</a>
            </td>
        </tr>
    </table>
</div>
@endsection
