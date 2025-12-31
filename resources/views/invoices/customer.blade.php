<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoiceNumber }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            background: #fff;
        }
        .invoice {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            border-bottom: 2px solid #6366f1;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #6366f1;
        }
        .invoice-title {
            text-align: right;
        }
        .invoice-title h1 {
            font-size: 32px;
            color: #6366f1;
            margin-bottom: 5px;
        }
        .invoice-title p {
            color: #666;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }
        .info-block {
            width: 45%;
        }
        .info-block h3 {
            font-size: 11px;
            text-transform: uppercase;
            color: #999;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }
        .info-block p {
            margin-bottom: 3px;
            color: #333;
        }
        .info-block .name {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th {
            background: #f8f9fa;
            padding: 12px 15px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            color: #666;
            border-bottom: 2px solid #e9ecef;
        }
        td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        .item-name {
            font-weight: 500;
        }
        .item-desc {
            font-size: 11px;
            color: #666;
            margin-top: 3px;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            width: 300px;
            margin-left: auto;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .totals-row.total {
            font-size: 16px;
            font-weight: bold;
            color: #6366f1;
            border-bottom: none;
            border-top: 2px solid #6366f1;
            padding-top: 12px;
            margin-top: 5px;
        }
        .status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid {
            background: #d1fae5;
            color: #059669;
        }
        .status-pending {
            background: #fef3c7;
            color: #d97706;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            text-align: center;
            color: #999;
            font-size: 11px;
        }
        .notes {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }
        .notes h4 {
            font-size: 12px;
            margin-bottom: 10px;
            color: #333;
        }
        .notes p {
            color: #666;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="invoice">
        <!-- Header -->
        <table style="margin-bottom: 40px; border-bottom: 2px solid #6366f1; padding-bottom: 20px;">
            <tr>
                <td style="border: none; padding: 0;">
                    <div class="logo">{{ $company['name'] }}</div>
                </td>
                <td style="border: none; padding: 0; text-align: right;">
                    <h1 style="font-size: 28px; color: #6366f1; margin: 0;">INVOICE</h1>
                    <p style="color: #666; margin-top: 5px;">{{ $invoiceNumber }}</p>
                </td>
            </tr>
        </table>

        <!-- Info Section -->
        <table style="margin-bottom: 40px;">
            <tr>
                <td style="border: none; padding: 0; width: 50%; vertical-align: top;">
                    <h3 style="font-size: 11px; text-transform: uppercase; color: #999; margin-bottom: 10px; letter-spacing: 1px;">From</h3>
                    <p style="font-weight: bold; font-size: 14px; margin-bottom: 8px;">{{ $company['name'] }}</p>
                    <p style="color: #666; margin-bottom: 3px;">{{ $company['address'] }}</p>
                    <p style="color: #666; margin-bottom: 3px;">{{ $company['city'] }}</p>
                    <p style="color: #666; margin-bottom: 3px;">{{ $company['country'] }}</p>
                    <p style="color: #666; margin-bottom: 3px;">{{ $company['email'] }}</p>
                    @if($company['tax_id'])
                    <p style="color: #666;">Tax ID: {{ $company['tax_id'] }}</p>
                    @endif
                </td>
                <td style="border: none; padding: 0; width: 50%; vertical-align: top;">
                    <h3 style="font-size: 11px; text-transform: uppercase; color: #999; margin-bottom: 10px; letter-spacing: 1px;">Bill To</h3>
                    <p style="font-weight: bold; font-size: 14px; margin-bottom: 8px;">{{ $customer['name'] }}</p>
                    <p style="color: #666; margin-bottom: 3px;">{{ $customer['email'] }}</p>
                    @if($customer['address'])
                    <p style="color: #666;">{{ $customer['address'] }}</p>
                    @endif
                </td>
            </tr>
        </table>

        <!-- Invoice Details -->
        <table style="margin-bottom: 30px;">
            <tr>
                <td style="border: none; padding: 10px 0; border-bottom: 1px solid #e9ecef;">
                    <strong>Invoice Date:</strong> {{ $invoiceDate->format('F d, Y') }}
                </td>
                <td style="border: none; padding: 10px 0; border-bottom: 1px solid #e9ecef;">
                    <strong>Order Number:</strong> {{ $order->order_number }}
                </td>
                <td style="border: none; padding: 10px 0; border-bottom: 1px solid #e9ecef; text-align: right;">
                    <span class="status status-{{ $order->payment_status === 'paid' ? 'paid' : 'pending' }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Item</th>
                    <th>License</th>
                    <th class="text-right">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        <div class="item-name">{{ $item->product->name ?? 'Product' }}</div>
                        <div class="item-desc">by {{ $item->seller->business_name ?? $item->seller->user->name ?? 'Seller' }}</div>
                    </td>
                    <td>{{ ucfirst($item->license_type ?? 'Regular') }}</td>
                    <td class="text-right">${{ number_format($item->price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <table style="width: 300px; margin-left: auto;">
            <tr>
                <td style="border: none; padding: 8px 0; border-bottom: 1px solid #e9ecef;">Subtotal</td>
                <td style="border: none; padding: 8px 0; border-bottom: 1px solid #e9ecef; text-align: right;">${{ number_format($order->subtotal ?? $order->total_amount, 2) }}</td>
            </tr>
            @if($order->discount_amount > 0)
            <tr>
                <td style="border: none; padding: 8px 0; border-bottom: 1px solid #e9ecef;">Discount</td>
                <td style="border: none; padding: 8px 0; border-bottom: 1px solid #e9ecef; text-align: right; color: #059669;">-${{ number_format($order->discount_amount, 2) }}</td>
            </tr>
            @endif
            @if($order->tax_amount > 0)
            <tr>
                <td style="border: none; padding: 8px 0; border-bottom: 1px solid #e9ecef;">Tax</td>
                <td style="border: none; padding: 8px 0; border-bottom: 1px solid #e9ecef; text-align: right;">${{ number_format($order->tax_amount, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td style="border: none; padding: 12px 0; border-top: 2px solid #6366f1; font-size: 16px; font-weight: bold; color: #6366f1;">Total</td>
                <td style="border: none; padding: 12px 0; border-top: 2px solid #6366f1; text-align: right; font-size: 16px; font-weight: bold; color: #6366f1;">${{ number_format($order->total_amount, 2) }}</td>
            </tr>
        </table>

        <!-- Notes -->
        <div class="notes">
            <h4>Notes</h4>
            <p>Thank you for your purchase! Your digital products are available for download in your account dashboard.</p>
            <p style="margin-top: 10px;">For support, please contact us at {{ $company['email'] }}</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>{{ $company['name'] }} &bull; {{ $company['website'] }}</p>
            <p style="margin-top: 5px;">This is a computer-generated invoice. No signature required.</p>
        </div>
    </div>
</body>
</html>
