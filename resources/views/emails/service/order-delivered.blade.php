@extends('emails.layouts.base')

@section('title', 'Order Delivered - ' . $order->order_number)

@section('preview')
Your order "{{ $order->title }}" has been delivered! Review and approve it now.
@endsection

@section('additional_styles')
<style>
    .header-gradient {
        background: linear-gradient(135deg, #3b82f6 0%, #6366f1 50%, #8b5cf6 100%);
        position: relative;
        overflow: hidden;
    }
    .delivery-card {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 2px solid #60a5fa;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
    }
    .file-item {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 14px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .file-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .action-buttons {
        display: flex;
        gap: 12px;
        justify-content: center;
    }
</style>
@endsection

@section('hero')
<div style="background: #ffffff;">
    <div style="text-align: center; padding: 20px 40px 32px;">
        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 20px; margin: 0 auto 24px; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 6L9 17L4 12" stroke="#ffffff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <span style="display: inline-block; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; padding: 6px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 16px;">Delivery Complete</span>
        <h1 style="font-size: 26px; font-weight: 700; color: #111827; margin: 0 0 8px;">Your Order Has Been Delivered!</h1>
        <p style="font-size: 16px; color: #6b7280; margin: 0;">{{ $order->seller->business_name ?? $order->seller->user->name }} has completed your order</p>
    </div>
</div>
@endsection

@section('content')
<div class="email-content">
    <!-- Delivery Card -->
    <div class="delivery-card">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td>
                    <p style="font-size: 12px; color: #3b82f6; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin: 0 0 4px;">Order</p>
                    <p style="font-size: 18px; font-weight: 700; color: #1e40af; margin: 0;">{{ $order->title }}</p>
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
                    <span style="color: #6b7280; font-size: 14px;">Seller</span>
                </td>
                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; text-align: right;">
                    <span style="color: #111827; font-weight: 600; font-size: 14px;">{{ $order->seller->business_name ?? $order->seller->user->name }}</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                    <span style="color: #6b7280; font-size: 14px;">Delivered On</span>
                </td>
                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; text-align: right;">
                    <span style="color: #111827; font-weight: 600; font-size: 14px;">{{ now()->format('F j, Y g:i A') }}</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 12px 0;">
                    <span style="color: #6b7280; font-size: 14px;">Order Value</span>
                </td>
                <td style="padding: 12px 0; text-align: right;">
                    <span style="color: #3b82f6; font-weight: 700; font-size: 16px;">{{ format_price($order->price) }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Delivery Message -->
    @if($delivery->message ?? false)
    <div style="background: #f9fafb; border-radius: 12px; padding: 20px; margin: 24px 0;">
        <p style="font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin: 0 0 8px;">Seller's Message</p>
        <p style="color: #374151; font-size: 14px; line-height: 1.6; margin: 0; white-space: pre-line;">{{ $delivery->message }}</p>
    </div>
    @endif

    <!-- Files -->
    @if(isset($delivery->files) && count($delivery->files) > 0)
    <h3 style="font-size: 14px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin: 24px 0 16px;">Delivered Files</h3>

    @foreach($delivery->files as $file)
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 10px; margin-bottom: 8px;">
        <tr>
            <td style="padding: 14px; width: 52px;">
                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </td>
            <td style="padding: 14px 14px 14px 0;">
                <p style="font-weight: 600; color: #111827; margin: 0; font-size: 14px;">{{ $file['name'] ?? 'File' }}</p>
                <p style="color: #6b7280; margin: 4px 0 0; font-size: 12px;">{{ $file['size'] ?? '' }}</p>
            </td>
        </tr>
    </table>
    @endforeach
    @endif

    <!-- Important Notice -->
    <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 1px solid #fcd34d; border-radius: 12px; padding: 20px; margin: 24px 0;">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="width: 40px; vertical-align: top;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 8V12M12 16H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#d97706" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </td>
                <td style="padding-left: 12px;">
                    <p style="font-weight: 600; color: #92400e; margin: 0 0 4px; font-size: 14px;">Action Required</p>
                    <p style="color: #a16207; margin: 0; font-size: 13px;">Please review the delivery and approve it if you're satisfied. If you need revisions, you can request them through the order page.</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Action Buttons -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td align="center" style="padding: 24px 0;">
                <a href="{{ url('/service-orders/' . $order->id) }}" style="display: inline-block; padding: 16px 32px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 12px; box-shadow: 0 4px 14px -3px rgba(16, 185, 129, 0.5); margin-right: 12px;">
                    Approve Delivery
                </a>
                <a href="{{ url('/service-orders/' . $order->id . '?revision=1') }}" style="display: inline-block; padding: 16px 24px; background: #ffffff; color: #374151; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 12px; border: 2px solid #e5e7eb;">
                    Request Revision
                </a>
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="text-align: center;">
                <p style="color: #6b7280; font-size: 14px; margin: 0;">Need help? <a href="{{ url('/support') }}" style="color: #3b82f6; text-decoration: none;">Contact Support</a></p>
            </td>
        </tr>
    </table>
</div>
@endsection
