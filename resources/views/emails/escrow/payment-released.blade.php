@extends('emails.layouts.base')

@section('title', 'Payment Released - ' . format_price($escrow->amount))

@section('preview')
Great news! {{ format_price($escrow->amount) }} has been released to your wallet.
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
        background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='20' cy='20' r='1'/%3E%3C/g%3E%3C/svg%3E");
    }
    .amount-card {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        border: 2px solid #10b981;
        border-radius: 20px;
        padding: 32px;
        text-align: center;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }
    .amount-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle, rgba(16,185,129,0.1) 0%, transparent 60%);
    }
    .confetti {
        position: absolute;
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }
</style>
@endsection

@section('hero')
<div style="background: #ffffff;">
    <div style="text-align: center; padding: 20px 40px 32px;">
        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 20px; margin: 0 auto 24px; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4);">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2V6M12 18V22M4.93 4.93L7.76 7.76M16.24 16.24L19.07 19.07M2 12H6M18 12H22M4.93 19.07L7.76 16.24M16.24 7.76L19.07 4.93" stroke="#ffffff" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
        <h1 style="font-size: 28px; font-weight: 700; color: #111827; margin: 0 0 8px;">Payment Released!</h1>
        <p style="font-size: 16px; color: #6b7280; margin: 0;">Your earnings are now in your wallet</p>
    </div>
</div>
@endsection

@section('content')
<div class="email-content">
    <!-- Amount Card -->
    <div class="amount-card">
        <p style="font-size: 14px; color: #059669; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; margin: 0 0 8px; position: relative;">Amount Released</p>
        <p style="font-size: 48px; font-weight: 800; color: #047857; margin: 0; position: relative;">{{ format_price($escrow->amount) }}</p>
        <p style="font-size: 14px; color: #10b981; margin: 12px 0 0; position: relative;">Added to your wallet</p>
    </div>

    <!-- Transaction Details -->
    <div class="info-card">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                    <span style="color: #6b7280; font-size: 14px;">Transaction ID</span>
                </td>
                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; text-align: right;">
                    <span style="color: #111827; font-weight: 600; font-size: 14px;">#{{ $escrow->id }}</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                    <span style="color: #6b7280; font-size: 14px;">Order</span>
                </td>
                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; text-align: right;">
                    <span style="color: #111827; font-weight: 600; font-size: 14px;">{{ $escrow->escrowable->title ?? $escrow->escrowable->order_number ?? 'N/A' }}</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                    <span style="color: #6b7280; font-size: 14px;">From</span>
                </td>
                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; text-align: right;">
                    <span style="color: #111827; font-weight: 600; font-size: 14px;">{{ $escrow->payer->name ?? 'Client' }}</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 12px 0;">
                    <span style="color: #6b7280; font-size: 14px;">Released On</span>
                </td>
                <td style="padding: 12px 0; text-align: right;">
                    <span style="color: #111827; font-weight: 600; font-size: 14px;">{{ now()->format('F j, Y g:i A') }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Wallet Balance -->
    <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-radius: 12px; padding: 20px; margin-top: 24px;">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td>
                    <p style="color: #0369a1; font-size: 14px; margin: 0;">New Wallet Balance</p>
                    <p style="color: #0c4a6e; font-size: 24px; font-weight: 700; margin: 8px 0 0;">{{ format_price($newBalance ?? $escrow->amount) }}</p>
                </td>
                <td style="text-align: right;">
                    <a href="{{ url('/wallet') }}" style="display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: #ffffff; font-size: 14px; font-weight: 600; text-decoration: none; border-radius: 10px;">
                        View Wallet
                    </a>
                </td>
            </tr>
        </table>
    </div>

    <!-- Withdraw CTA -->
    <div style="text-align: center; margin: 32px 0;">
        <a href="{{ url('/seller/payouts') }}" style="display: inline-block; padding: 16px 32px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 12px; box-shadow: 0 4px 14px -3px rgba(16, 185, 129, 0.5);">
            Request Payout
        </a>
    </div>

    <div class="divider"></div>

    <!-- Thank You -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="text-align: center;">
                <p style="color: #6b7280; font-size: 14px; margin: 0;">Thank you for being part of Codexse!</p>
            </td>
        </tr>
    </table>
</div>
@endsection
