@extends('emails.layouts.base')

@section('title', 'Dispute Resolved')

@section('preview')
Your dispute has been resolved. A decision has been made regarding your case.
@endsection

@section('additional_styles')
<style>
    .header-gradient {
        background: linear-gradient(135deg, #10B981 0%, #059669 50%, #047857 100%);
        position: relative;
        overflow: hidden;
    }
    .resolution-card {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        border: 1px solid #a7f3d0;
        border-radius: 16px;
        overflow: hidden;
    }
    .resolution-header {
        background: linear-gradient(135deg, #10b981, #059669);
        padding: 20px;
        text-align: center;
    }
    .fund-box {
        background: white;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        flex: 1;
    }
    .appeal-notice {
        background: #fef3c7;
        border-radius: 12px;
        padding: 20px;
        border-left: 4px solid #f59e0b;
    }
</style>
@endsection

@section('hero')
<div style="background: #ffffff;">
    <div style="text-align: center; padding: 20px 40px 32px;">
        <div style="width: 80px; height: 80px; background: #ffffff; border-radius: 20px; margin: 0 auto 24px; box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.3); border: 2px solid #d1fae5;">
            <table role="presentation" width="100%" height="100%">
                <tr>
                    <td align="center" valign="middle">
                        <span style="font-size: 36px;">&#9878;</span>
                    </td>
                </tr>
            </table>
        </div>
        <h1 style="font-size: 28px; font-weight: 700; color: #111827; margin: 0 0 8px;">Dispute Resolved</h1>
        <p style="font-size: 16px; color: #6b7280; margin: 0;">A decision has been made</p>
    </div>
</div>
@endsection

@section('content')
<div style="padding: 0 40px 40px;">
    <!-- Greeting -->
    <p style="font-size: 16px; color: #374151; margin: 0 0 16px;">
        Hello {{ $recipient->name }},
    </p>
    <p style="font-size: 16px; color: #6b7280; margin: 0 0 24px;">
        After careful review of all evidence and communications, our resolution team has reached a decision on your dispute. Please review the resolution details below.
    </p>

    <!-- Resolution Card -->
    <div class="resolution-card">
        <div class="resolution-header">
            <p style="font-size: 12px; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px; margin: 0 0 8px;">Resolution Decision</p>
            <p style="font-size: 24px; color: white; font-weight: 700; margin: 0;">{{ ucfirst($dispute->resolution ?? 'Resolved') }}</p>
        </div>
        <div style="padding: 20px;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px dashed #a7f3d0;">
                        <span style="color: #059669; font-size: 14px;">Dispute ID</span>
                    </td>
                    <td style="padding: 12px 0; text-align: right; border-bottom: 1px dashed #a7f3d0;">
                        <span style="color: #047857; font-weight: 600;">#{{ $dispute->id }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px dashed #a7f3d0;">
                        <span style="color: #059669; font-size: 14px;">Original Reason</span>
                    </td>
                    <td style="padding: 12px 0; text-align: right; border-bottom: 1px dashed #a7f3d0;">
                        <span style="color: #047857; font-weight: 600;">{{ ucfirst(str_replace('_', ' ', $dispute->reason)) }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 12px 0;">
                        <span style="color: #059669; font-size: 14px;">Resolved Date</span>
                    </td>
                    <td style="padding: 12px 0; text-align: right;">
                        <span style="color: #047857; font-weight: 600;">{{ ($dispute->resolved_at ?? now())->format('M d, Y') }}</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    @if($dispute->resolution_notes ?? null)
    <!-- Resolution Notes -->
    <div style="background: #f3f4f6; border-radius: 16px; padding: 20px; margin-top: 24px;">
        <p style="font-size: 14px; font-weight: 600; color: #374151; margin: 0 0 12px;">Resolution Notes</p>
        <div style="background: white; border-radius: 10px; padding: 16px; border-left: 4px solid #10b981;">
            <p style="font-size: 14px; color: #6b7280; margin: 0; line-height: 1.7;">{{ $dispute->resolution_notes }}</p>
        </div>
    </div>
    @endif

    @if(($dispute->buyer_refund ?? 0) > 0 || ($dispute->seller_payout ?? 0) > 0)
    <!-- Fund Distribution -->
    <div style="background: #eff6ff; border-radius: 16px; padding: 20px; margin-top: 24px; border: 1px solid #bfdbfe;">
        <p style="font-size: 14px; font-weight: 600; color: #1e40af; margin: 0 0 16px;">Fund Distribution</p>

        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                @if(($dispute->buyer_refund ?? 0) > 0)
                <td style="width: 48%; padding-right: 2%;">
                    <div class="fund-box">
                        <p style="font-size: 12px; color: #6b7280; margin: 0 0 5px;">Buyer Refund</p>
                        <p style="font-size: 24px; font-weight: 700; color: #3b82f6; margin: 0;">${{ number_format($dispute->buyer_refund, 2) }}</p>
                    </div>
                </td>
                @endif
                @if(($dispute->seller_payout ?? 0) > 0)
                <td style="width: 48%; padding-left: 2%;">
                    <div class="fund-box">
                        <p style="font-size: 12px; color: #6b7280; margin: 0 0 5px;">Seller Payout</p>
                        <p style="font-size: 24px; font-weight: 700; color: #10b981; margin: 0;">${{ number_format($dispute->seller_payout, 2) }}</p>
                    </div>
                </td>
                @endif
            </tr>
        </table>
    </div>
    @endif

    <!-- Appeal Notice -->
    <div class="appeal-notice" style="margin-top: 24px;">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td style="vertical-align: top; padding-right: 12px;">
                    <span style="font-size: 20px;">&#128227;</span>
                </td>
                <td>
                    <p style="font-size: 14px; font-weight: 600; color: #92400e; margin: 0 0 4px;">Not satisfied with the decision?</p>
                    <p style="font-size: 14px; color: #b45309; margin: 0;">If you believe this decision was made in error, you may request an appeal within 7 days by contacting our support team with any additional evidence.</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- CTA Button -->
    <div style="text-align: center; margin-top: 32px;">
        <a href="{{ url('/disputes/' . $dispute->id) }}" style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 30px; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);">
            View Full Resolution
        </a>
    </div>
</div>
@endsection
