<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoiceNumber }}</title>
    <style>
        @page {
            margin: 0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #1f2937;
            background: #fff;
        }
        .invoice-container {
            position: relative;
        }

        /* Header with gradient background */
        .header {
            background: linear-gradient(135deg, #6366f1 0%, #06b6d4 100%);
            padding: 40px 50px;
            color: white;
        }
        .header-content {
            display: table;
            width: 100%;
        }
        .header-left {
            display: table-cell;
            vertical-align: middle;
            width: 50%;
        }
        .header-right {
            display: table-cell;
            vertical-align: middle;
            width: 50%;
            text-align: right;
        }
        .logo-section {
            display: flex;
            align-items: center;
        }
        .logo {
            width: 48px;
            height: 48px;
            background: rgba(255,255,255,0.2);
            border-radius: 12px;
            display: inline-block;
            vertical-align: middle;
            text-align: center;
            line-height: 48px;
            margin-right: 12px;
        }
        .logo svg {
            width: 28px;
            height: 28px;
            vertical-align: middle;
            margin-top: 10px;
        }
        .brand-name {
            display: inline-block;
            vertical-align: middle;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .invoice-badge {
            background: rgba(255,255,255,0.2);
            border-radius: 8px;
            padding: 20px 30px;
            display: inline-block;
        }
        .invoice-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        .invoice-number {
            font-size: 24px;
            font-weight: 700;
        }

        /* Main content */
        .content {
            padding: 40px 50px;
        }

        /* Info cards */
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 40px;
        }
        .info-card {
            display: table-cell;
            width: 33.33%;
            padding-right: 20px;
            vertical-align: top;
        }
        .info-card:last-child {
            padding-right: 0;
        }
        .info-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #9ca3af;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .info-value {
            color: #1f2937;
        }
        .info-value .name {
            font-size: 15px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 5px;
        }
        .info-value p {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 2px;
        }

        /* Status badge */
        .status-section {
            text-align: right;
        }
        .status {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .status-completed {
            background: #d1fae5;
            color: #059669;
        }
        .status-pending {
            background: #fef3c7;
            color: #d97706;
        }
        .status-refunded {
            background: #fee2e2;
            color: #dc2626;
        }
        .order-meta {
            margin-top: 15px;
            font-size: 12px;
            color: #6b7280;
        }

        /* Items table */
        .items-section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #9ca3af;
            margin-bottom: 15px;
            font-weight: 600;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
        }
        table.items thead th {
            background: #f9fafb;
            padding: 14px 16px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6b7280;
            font-weight: 600;
            border-bottom: 2px solid #e5e7eb;
        }
        table.items thead th:last-child {
            text-align: right;
        }
        table.items tbody td {
            padding: 16px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: top;
        }
        table.items tbody td:last-child {
            text-align: right;
        }
        .item-name {
            font-weight: 600;
            color: #111827;
            font-size: 13px;
        }
        .item-seller {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 3px;
        }
        .item-license {
            display: inline-block;
            background: #ede9fe;
            color: #7c3aed;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .item-price {
            font-weight: 600;
            color: #111827;
            font-size: 13px;
        }

        /* Totals section */
        .totals-wrapper {
            display: table;
            width: 100%;
        }
        .totals-spacer {
            display: table-cell;
            width: 60%;
        }
        .totals-section {
            display: table-cell;
            width: 40%;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 10px 0;
            border: none;
        }
        .totals-table .label {
            color: #6b7280;
            font-size: 12px;
        }
        .totals-table .value {
            text-align: right;
            font-weight: 500;
            color: #1f2937;
        }
        .totals-table .discount .value {
            color: #059669;
        }
        .totals-table .total-row td {
            padding-top: 15px;
            border-top: 2px solid #e5e7eb;
        }
        .totals-table .total-row .label {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
        }
        .totals-table .total-row .value {
            font-size: 20px;
            font-weight: 700;
            color: #6366f1;
        }

        /* Notes section */
        .notes-section {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 12px;
            padding: 25px;
            margin-top: 40px;
            border: 1px solid #e2e8f0;
        }
        .notes-title {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 10px;
        }
        .notes-text {
            font-size: 12px;
            color: #64748b;
            line-height: 1.7;
        }

        /* Footer */
        .footer {
            background: #f9fafb;
            padding: 25px 50px;
            margin-top: 40px;
            border-top: 1px solid #e5e7eb;
        }
        .footer-content {
            display: table;
            width: 100%;
        }
        .footer-left {
            display: table-cell;
            width: 50%;
            vertical-align: middle;
        }
        .footer-right {
            display: table-cell;
            width: 50%;
            vertical-align: middle;
            text-align: right;
        }
        .footer-text {
            font-size: 11px;
            color: #9ca3af;
        }
        .footer-brand {
            color: #6366f1;
            font-weight: 600;
        }

        /* Watermark */
        .watermark {
            position: fixed;
            bottom: 100px;
            right: 50px;
            opacity: 0.03;
            font-size: 120px;
            font-weight: 900;
            color: #6366f1;
            transform: rotate(-30deg);
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Watermark -->
        <div class="watermark">CODEXSE</div>

        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="header-left">
                    <span class="logo">
                        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 7L4 12L9 17"/>
                            <path d="M15 7L20 12L15 17"/>
                            <path d="M10.5 9.5L13.5 14.5"/>
                            <path d="M13.5 9.5L10.5 14.5"/>
                        </svg>
                    </span>
                    <span class="brand-name">{{ $company['name'] }}</span>
                </div>
                <div class="header-right">
                    <div class="invoice-badge">
                        <div class="invoice-title">Invoice</div>
                        <div class="invoice-number">{{ $invoiceNumber }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Info Grid -->
            <div class="info-grid">
                <div class="info-card">
                    <div class="info-label">From</div>
                    <div class="info-value">
                        <div class="name">{{ $company['name'] }}</div>
                        <p>{{ $company['address'] }}</p>
                        <p>{{ $company['city'] }}</p>
                        <p>{{ $company['country'] }}</p>
                        <p>{{ $company['email'] }}</p>
                        @if($company['tax_id'])
                        <p style="margin-top: 8px; font-weight: 500;">Tax ID: {{ $company['tax_id'] }}</p>
                        @endif
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-label">Bill To</div>
                    <div class="info-value">
                        <div class="name">{{ $customer['name'] }}</div>
                        <p>{{ $customer['email'] }}</p>
                        @if($customer['address'])
                        <p>{{ is_array($customer['address']) ? implode(', ', array_filter($customer['address'])) : $customer['address'] }}</p>
                        @endif
                        @if($order->billing_state)
                        <p>{{ config('tax.states.' . $order->billing_state, $order->billing_state) }}</p>
                        @endif
                    </div>
                </div>
                <div class="info-card status-section">
                    <div class="info-label">Status</div>
                    @php
                        $statusClass = match($order->status) {
                            'completed' => 'status-completed',
                            'refunded' => 'status-refunded',
                            default => 'status-pending'
                        };
                    @endphp
                    <span class="status {{ $statusClass }}">{{ ucfirst($order->status ?? 'pending') }}</span>
                    <div class="order-meta">
                        <p><strong>Order:</strong> {{ $order->order_number }}</p>
                        <p><strong>Date:</strong> {{ $invoiceDate->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="items-section">
                <div class="section-title">Order Items</div>
                <table class="items">
                    <thead>
                        <tr>
                            <th style="width: 50%;">Product</th>
                            <th>License</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>
                                <div class="item-name">{{ $item->product->name ?? 'Digital Product' }}</div>
                                <div class="item-seller">by {{ $item->seller->business_name ?? $item->seller->user->name ?? 'Seller' }}</div>
                            </td>
                            <td>
                                <span class="item-license">{{ ucfirst($item->license_type ?? 'Regular') }}</span>
                            </td>
                            <td>
                                <span class="item-price">{{ format_price($item->price ?? 0) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div class="totals-wrapper">
                <div class="totals-spacer"></div>
                <div class="totals-section">
                    <table class="totals-table">
                        <tr>
                            <td class="label">Subtotal</td>
                            <td class="value">{{ format_price($order->subtotal ?? $order->total ?? 0) }}</td>
                        </tr>
                        @if(($order->discount ?? 0) > 0)
                        <tr class="discount">
                            <td class="label">Discount</td>
                            <td class="value">-{{ format_price($order->discount) }}</td>
                        </tr>
                        @endif
                        @if(($order->tax_amount ?? 0) > 0)
                        <tr>
                            <td class="label">
                                {{ \App\Models\Setting::get('tax_label', 'Sales Tax') }}
                                @if($order->tax_rate)
                                ({{ number_format($order->tax_rate, 2) }}%)
                                @endif
                            </td>
                            <td class="value">{{ format_price($order->tax_amount) }}</td>
                        </tr>
                        @endif
                        <tr class="total-row">
                            <td class="label">Total</td>
                            <td class="value">{{ format_price($order->total ?? 0) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Notes -->
            <div class="notes-section">
                <div class="notes-title">Thank you for your purchase!</div>
                <div class="notes-text">
                    Your digital products are now available for download in your account dashboard.
                    If you have any questions or need assistance, please don't hesitate to contact us at
                    <strong>{{ $company['email'] }}</strong>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-content">
                <div class="footer-left">
                    <div class="footer-text">
                        <span class="footer-brand">{{ $company['name'] }}</span> &bull; {{ $company['website'] }}
                    </div>
                </div>
                <div class="footer-right">
                    <div class="footer-text">
                        This is a computer-generated invoice. No signature required.
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
