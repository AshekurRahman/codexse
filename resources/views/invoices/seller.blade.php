<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Statement {{ $invoiceNumber }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            background: #fff;
        }
        .invoice { max-width: 800px; margin: 0 auto; padding: 30px; }
        .logo { font-size: 28px; font-weight: bold; color: #6366f1; }
        .status { display: inline-block; padding: 5px 15px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .status-paid { background: #d1fae5; color: #059669; }
        .text-right { text-align: right; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8f9fa; padding: 12px 15px; text-align: left; font-size: 11px; text-transform: uppercase; color: #666; border-bottom: 2px solid #e9ecef; }
        td { padding: 15px; border-bottom: 1px solid #e9ecef; }
        .notes { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-top: 30px; }
        .notes h4 { font-size: 12px; margin-bottom: 10px; color: #333; }
        .notes p { color: #666; font-size: 11px; }
        .footer { margin-top: 50px; padding-top: 20px; border-top: 1px solid #e9ecef; text-align: center; color: #999; font-size: 11px; }
    </style>
</head>
<body>
    <div class="invoice">
        <!-- Header -->
        <table style="margin-bottom: 40px; border-bottom: 2px solid #6366f1;">
            <tr>
                <td style="border: none; padding: 0 0 20px 0;">
                    <div class="logo">{{ $company['name'] }}</div>
                </td>
                <td style="border: none; padding: 0 0 20px 0; text-align: right;">
                    <h1 style="font-size: 24px; color: #6366f1; margin: 0;">SALES STATEMENT</h1>
                    <p style="color: #666; margin-top: 5px;">{{ $invoiceNumber }}</p>
                </td>
            </tr>
        </table>

        <!-- Info Section -->
        <table style="margin-bottom: 40px;">
            <tr>
                <td style="border: none; padding: 0; width: 50%; vertical-align: top;">
                    <h3 style="font-size: 11px; text-transform: uppercase; color: #999; margin-bottom: 10px;">Seller</h3>
                    <p style="font-weight: bold; font-size: 14px; margin-bottom: 8px;">{{ $seller->business_name ?? $seller->user->name }}</p>
                    <p style="color: #666;">{{ $seller->user->email }}</p>
                </td>
                <td style="border: none; padding: 0; width: 50%; vertical-align: top;">
                    <h3 style="font-size: 11px; text-transform: uppercase; color: #999; margin-bottom: 10px;">Order Details</h3>
                    <p style="margin-bottom: 3px;"><strong>Order:</strong> {{ $order->order_number }}</p>
                    <p style="margin-bottom: 3px;"><strong>Date:</strong> {{ $invoiceDate->format('F d, Y') }}</p>
                    <p style="margin-bottom: 3px;"><strong>Customer:</strong> {{ $order->user->name }}</p>
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Product</th>
                    <th>License</th>
                    <th class="text-right">Sale Price</th>
                    <th class="text-right">Your Earnings</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item->product->name ?? 'Product' }}</td>
                    <td>{{ ucfirst($item->license_type ?? 'Regular') }}</td>
                    <td class="text-right">${{ number_format($item->price, 2) }}</td>
                    <td class="text-right" style="color: #059669; font-weight: bold;">${{ number_format($item->seller_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <table style="width: 300px; margin-left: auto; margin-top: 20px;">
            <tr>
                <td style="border: none; padding: 12px 0; border-top: 2px solid #6366f1; font-size: 16px; font-weight: bold; color: #6366f1;">Total Earnings</td>
                <td style="border: none; padding: 12px 0; border-top: 2px solid #6366f1; text-align: right; font-size: 16px; font-weight: bold; color: #059669;">${{ number_format($subtotal, 2) }}</td>
            </tr>
        </table>

        <!-- Notes -->
        <div class="notes">
            <h4>Payment Information</h4>
            <p>Your earnings from this sale have been credited to your seller balance.</p>
            <p style="margin-top: 10px;">You can request a withdrawal from your seller dashboard once you meet the minimum payout threshold.</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>{{ $company['name'] }} Seller Statement</p>
            <p style="margin-top: 5px;">Generated on {{ now()->format('F d, Y') }}</p>
        </div>
    </div>
</body>
</html>
