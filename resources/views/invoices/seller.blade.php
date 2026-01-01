<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Statement {{ $invoiceNumber }}</title>
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
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
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
        table.items thead th.earnings-col {
            text-align: right;
            color: #059669;
        }
        table.items tbody td {
            padding: 16px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }
        table.items tbody td:last-child {
            text-align: right;
        }
        .item-name {
            font-weight: 600;
            color: #111827;
            font-size: 13px;
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
            color: #6b7280;
            font-size: 12px;
        }
        .item-earnings {
            font-weight: 700;
            color: #059669;
            font-size: 14px;
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
        .totals-box {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 2px solid #10b981;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
        }
        .totals-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #059669;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .totals-value {
            font-size: 32px;
            font-weight: 800;
            color: #047857;
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
            color: #059669;
            font-weight: 600;
        }

        /* Watermark */
        .watermark {
            position: fixed;
            bottom: 100px;
            right: 50px;
            opacity: 0.03;
            font-size: 100px;
            font-weight: 900;
            color: #059669;
            transform: rotate(-30deg);
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Watermark -->
        <div class="watermark">SELLER</div>

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
                        <div class="invoice-title">Sales Statement</div>
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
                    <div class="info-label">Seller</div>
                    <div class="info-value">
                        <div class="name">{{ $seller->business_name ?? $seller->user->name }}</div>
                        <p>{{ $seller->user->email }}</p>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-label">Order Details</div>
                    <div class="info-value">
                        <p><strong>Order:</strong> {{ $order->order_number }}</p>
                        <p><strong>Date:</strong> {{ $invoiceDate->format('M d, Y') }}</p>
                        <p><strong>Customer:</strong> {{ $order->user->name }}</p>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-label">Statement Date</div>
                    <div class="info-value">
                        <div class="name">{{ now()->format('F d, Y') }}</div>
                        <p>Generated automatically</p>
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="items-section">
                <div class="section-title">Your Sales</div>
                <table class="items">
                    <thead>
                        <tr>
                            <th style="width: 40%;">Product</th>
                            <th>License</th>
                            <th>Sale Price</th>
                            <th class="earnings-col">Your Earnings</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr>
                            <td>
                                <div class="item-name">{{ $item->product->name ?? 'Digital Product' }}</div>
                            </td>
                            <td>
                                <span class="item-license">{{ ucfirst($item->license_type ?? 'Regular') }}</span>
                            </td>
                            <td>
                                <span class="item-price">{{ format_price($item->price ?? 0) }}</span>
                            </td>
                            <td>
                                <span class="item-earnings">{{ format_price($item->seller_amount ?? 0) }}</span>
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
                    <div class="totals-box">
                        <div class="totals-label">Total Earnings</div>
                        <div class="totals-value">{{ format_price($subtotal ?? 0) }}</div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="notes-section">
                <div class="notes-title">Payment Information</div>
                <div class="notes-text">
                    Your earnings from this sale have been credited to your wallet.
                    You can request a withdrawal from your wallet once you meet the minimum payout threshold.
                    For any questions, please contact us at <strong>{{ $company['email'] }}</strong>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-content">
                <div class="footer-left">
                    <div class="footer-text">
                        <span class="footer-brand">{{ $company['name'] }}</span> &bull; Seller Statement
                    </div>
                </div>
                <div class="footer-right">
                    <div class="footer-text">
                        Generated on {{ now()->format('F d, Y \a\t h:i A') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
