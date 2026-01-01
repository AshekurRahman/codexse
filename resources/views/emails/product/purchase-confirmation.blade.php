@extends('emails.layouts.base')

@section('title', 'Order Confirmation - ' . $order->order_number)

@section('preview')
Your order #{{ $order->order_number }} has been confirmed. Thank you for your purchase!
@endsection

@section('hero')
<div style="background: #ffffff; padding: 40px; text-align: center;">
    <!-- Success Icon -->
    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 20px; margin: 0 auto 24px; box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.3);">
        <table role="presentation" width="100%" height="100%">
            <tr>
                <td align="center" valign="middle">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </td>
            </tr>
        </table>
    </div>
    <h1 style="font-size: 28px; font-weight: 700; color: #0f172a; margin: 0 0 12px; line-height: 1.3;">Order Confirmed!</h1>
    <p style="font-size: 16px; color: #64748b; margin: 0;">Thank you for your purchase, {{ $customer->name }}!</p>
</div>
@endsection

@section('content')
<div style="padding: 0 40px 40px;">
    <!-- Order Details Card -->
    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 24px;">
        <div style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); padding: 16px 24px;">
            <p style="font-size: 12px; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px; margin: 0 0 4px;">Order Number</p>
            <p style="font-size: 18px; color: #ffffff; font-weight: 700; margin: 0; font-family: ui-monospace, monospace;">#{{ $order->order_number }}</p>
        </div>
        <div style="padding: 24px;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                        <span style="color: #64748b; font-size: 14px;">Order Date</span>
                    </td>
                    <td style="padding: 12px 0; text-align: right; border-bottom: 1px solid #e2e8f0;">
                        <span style="color: #0f172a; font-weight: 600; font-size: 14px;">{{ $order->created_at->format('F j, Y') }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 12px 0;">
                        <span style="color: #64748b; font-size: 14px;">Payment Method</span>
                    </td>
                    <td style="padding: 12px 0; text-align: right;">
                        <span style="color: #0f172a; font-weight: 600; font-size: 14px;">{{ ucfirst($order->payment_method ?? 'Card') }}</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Products Section -->
    <h3 style="font-size: 16px; font-weight: 600; color: #0f172a; margin: 0 0 16px;">Your Products</h3>

    @foreach($order->items as $item)
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 12px;">
        <tr>
            <td style="padding: 16px; width: 80px; vertical-align: top;">
                @if($item->product && $item->product->thumbnail)
                <img src="{{ asset('storage/' . $item->product->thumbnail) }}" alt="{{ $item->product->name }}" style="width: 80px; height: 80px; border-radius: 8px; object-fit: cover; display: block;">
                @else
                <div style="width: 80px; height: 80px; border-radius: 8px; background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);"></div>
                @endif
            </td>
            <td style="padding: 16px; vertical-align: top;">
                <p style="font-weight: 600; color: #0f172a; font-size: 15px; margin: 0 0 4px;">{{ $item->product->name ?? 'Digital Product' }}</p>
                <p style="color: #64748b; font-size: 13px; margin: 0 0 8px;">by {{ $item->seller->business_name ?? $item->seller->user->name ?? 'Seller' }}</p>
                <span style="display: inline-block; background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); color: #6366f1; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; text-transform: uppercase;">{{ ucfirst($item->license_type ?? 'Regular') }} License</span>
            </td>
            <td style="padding: 16px; text-align: right; vertical-align: top; white-space: nowrap;">
                <span style="font-weight: 700; color: #0f172a; font-size: 16px;">{{ format_price($item->price) }}</span>
            </td>
        </tr>
    </table>
    @endforeach

    <!-- Order Summary -->
    <div style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #86efac; border-radius: 12px; padding: 24px; margin-top: 24px;">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="padding: 8px 0;">
                    <span style="color: #166534; font-size: 14px;">Subtotal</span>
                </td>
                <td style="padding: 8px 0; text-align: right;">
                    <span style="color: #166534; font-weight: 600; font-size: 14px;">{{ format_price($order->subtotal ?? $order->total) }}</span>
                </td>
            </tr>
            @if(($order->discount ?? 0) > 0)
            <tr>
                <td style="padding: 8px 0;">
                    <span style="color: #166534; font-size: 14px;">Discount</span>
                </td>
                <td style="padding: 8px 0; text-align: right;">
                    <span style="color: #166534; font-weight: 600; font-size: 14px;">-{{ format_price($order->discount) }}</span>
                </td>
            </tr>
            @endif
            @if(($order->tax_amount ?? 0) > 0)
            <tr>
                <td style="padding: 8px 0;">
                    <span style="color: #166534; font-size: 14px;">Tax</span>
                </td>
                <td style="padding: 8px 0; text-align: right;">
                    <span style="color: #166534; font-weight: 600; font-size: 14px;">{{ format_price($order->tax_amount) }}</span>
                </td>
            </tr>
            @endif
            <tr>
                <td colspan="2" style="padding-top: 12px;">
                    <div style="border-top: 2px solid #86efac;"></div>
                </td>
            </tr>
            <tr>
                <td style="padding: 16px 0 0;">
                    <span style="color: #166534; font-size: 18px; font-weight: 700;">Total Paid</span>
                </td>
                <td style="padding: 16px 0 0; text-align: right;">
                    <span style="color: #166534; font-weight: 700; font-size: 18px;">{{ format_price($order->total) }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Download Section -->
    <div style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 1px solid #93c5fd; border-radius: 12px; padding: 32px; margin-top: 24px; text-align: center;">
        <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 14px; margin: 0 auto 16px;">
            <table role="presentation" width="100%" height="100%">
                <tr>
                    <td align="center" valign="middle">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 15V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V15M7 10L12 15M12 15L17 10M12 15V3" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </td>
                </tr>
            </table>
        </div>
        <h3 style="font-size: 18px; font-weight: 600; color: #1e40af; margin: 0 0 8px;">Ready to Download</h3>
        <p style="font-size: 14px; color: #3b82f6; margin: 0 0 20px;">Your files are waiting for you in your dashboard</p>

        <a href="{{ url('/purchases') }}" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 10px; box-shadow: 0 4px 14px -3px rgba(59, 130, 246, 0.5);">
            Download Your Files
        </a>
    </div>

    <!-- Divider -->
    <div style="height: 1px; background: linear-gradient(90deg, transparent 0%, #e2e8f0 50%, transparent 100%); margin: 32px 0;"></div>

    <!-- Help Section -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="text-align: center;">
                <p style="color: #64748b; font-size: 14px; margin: 0 0 8px;">Need help with your purchase?</p>
                <a href="{{ url('/support') }}" style="color: #6366f1; font-size: 14px; text-decoration: none; font-weight: 500;">Contact Support</a>
            </td>
        </tr>
    </table>
</div>
@endsection
