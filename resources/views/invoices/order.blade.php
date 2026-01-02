<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice_number }}</title>
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
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            border-bottom: 2px solid #7c3aed;
            padding-bottom: 20px;
        }
        .company-info {
            float: left;
            width: 50%;
        }
        .invoice-info {
            float: right;
            width: 50%;
            text-align: right;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #7c3aed;
            margin-bottom: 10px;
        }
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .invoice-number {
            font-size: 14px;
            color: #666;
        }
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        .addresses {
            margin-bottom: 30px;
        }
        .bill-to {
            float: left;
            width: 50%;
        }
        .invoice-details {
            float: right;
            width: 50%;
            text-align: right;
        }
        .section-title {
            font-size: 10px;
            text-transform: uppercase;
            color: #999;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }
        .customer-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th {
            background-color: #7c3aed;
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }
        th:last-child {
            text-align: right;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        td:last-child {
            text-align: right;
        }
        .item-name {
            font-weight: bold;
        }
        .item-description {
            color: #666;
            font-size: 11px;
        }
        .totals {
            float: right;
            width: 300px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .totals-row.total {
            font-size: 16px;
            font-weight: bold;
            border-bottom: none;
            border-top: 2px solid #333;
            padding-top: 12px;
        }
        .totals-label {
            float: left;
            width: 50%;
        }
        .totals-value {
            float: right;
            width: 50%;
            text-align: right;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-completed {
            background-color: #10b981;
            color: white;
        }
        .status-pending {
            background-color: #f59e0b;
            color: white;
        }
        .status-refunded {
            background-color: #ef4444;
            color: white;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #999;
            font-size: 10px;
        }
        .payment-info {
            margin-top: 30px;
            padding: 15px;
            background-color: #f9fafb;
            border-radius: 8px;
        }
        .payment-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header clearfix">
            <div class="company-info">
                <div class="company-name">{{ $company['name'] }}</div>
                @if($company['address'])
                    <div>{{ $company['address'] }}</div>
                @endif
                @if($company['email'])
                    <div>{{ $company['email'] }}</div>
                @endif
                @if($company['phone'])
                    <div>{{ $company['phone'] }}</div>
                @endif
            </div>
            <div class="invoice-info">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-number">{{ $invoice_number }}</div>
            </div>
        </div>

        <div class="addresses clearfix">
            <div class="bill-to">
                <div class="section-title">Bill To</div>
                <div class="customer-name">{{ $order->user->name }}</div>
                <div>{{ $order->user->email }}</div>
                @if($order->billing_state)
                    <div>{{ $order->billing_state }}</div>
                @endif
            </div>
            <div class="invoice-details">
                <div class="section-title">Invoice Details</div>
                <div><strong>Invoice Date:</strong> {{ $invoice_date }}</div>
                <div><strong>Order #:</strong> {{ $order->order_number }}</div>
                <div><strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}</div>
                <div style="margin-top: 10px;">
                    <span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 50%">Item</th>
                    <th style="width: 15%">License</th>
                    <th style="width: 15%">Qty</th>
                    <th style="width: 20%">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        <div class="item-name">{{ $item->product_name ?? $item->product?->name ?? 'Product' }}</div>
                        @if($item->seller)
                            <div class="item-description">by {{ $item->seller->business_name }}</div>
                        @endif
                    </td>
                    <td>{{ ucfirst($item->license_type ?? 'Regular') }}</td>
                    <td>1</td>
                    <td>${{ number_format($item->price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="clearfix">
            <div class="totals">
                <div class="totals-row clearfix">
                    <div class="totals-label">Subtotal</div>
                    <div class="totals-value">${{ number_format($order->subtotal, 2) }}</div>
                </div>
                @if($order->discount > 0)
                <div class="totals-row clearfix">
                    <div class="totals-label">Discount</div>
                    <div class="totals-value">-${{ number_format($order->discount, 2) }}</div>
                </div>
                @endif
                @if($order->tax_amount > 0)
                <div class="totals-row clearfix">
                    <div class="totals-label">Tax ({{ $order->tax_rate ?? 0 }}%)</div>
                    <div class="totals-value">${{ number_format($order->tax_amount, 2) }}</div>
                </div>
                @endif
                <div class="totals-row total clearfix">
                    <div class="totals-label">Total</div>
                    <div class="totals-value">${{ number_format($order->total, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="payment-info">
            <div class="payment-title">Payment Information</div>
            <div>Payment Method: {{ ucfirst($order->payment_method) }}</div>
            @if($order->paid_at)
                <div>Paid on: {{ $order->paid_at->format('F j, Y g:i A') }}</div>
            @endif
            @if($order->stripe_payment_intent_id)
                <div>Transaction ID: {{ $order->stripe_payment_intent_id }}</div>
            @elseif($order->paypal_order_id)
                <div>Transaction ID: {{ $order->paypal_order_id }}</div>
            @endif
        </div>

        <div class="footer">
            <p>Thank you for your purchase!</p>
            <p>{{ $company['name'] }} | {{ $company['email'] }}</p>
        </div>
    </div>
</body>
</html>
