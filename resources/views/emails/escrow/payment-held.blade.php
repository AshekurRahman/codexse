@extends('emails.layouts.base')

@section('title', 'Payment Secured in Escrow')

@section('preview')
Payment secured! ${{ number_format($escrow->amount, 2) }} is now held safely in escrow protection.
@endsection

@section('additional_styles')
<style>
    .header-gradient {
        background: linear-gradient(135deg, #3B82F6 0%, #2563EB 50%, #1D4ED8 100%);
        position: relative;
        overflow: hidden;
    }
    .amount-card {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 1px solid #93c5fd;
        border-radius: 16px;
        overflow: hidden;
    }
    .amount-header {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        padding: 20px;
        text-align: center;
    }
    .step-circle {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border-radius: 50%;
        color: white;
        font-weight: 700;
        font-size: 16px;
        line-height: 40px;
        text-align: center;
        margin-right: 15px;
        flex-shrink: 0;
    }
    .step-circle.complete {
        background: linear-gradient(135deg, #10b981, #059669);
    }
</style>
@endsection

@section('hero')
<div style="background: #ffffff;">
    <div style="text-align: center; padding: 20px 40px 32px;">
        <div style="width: 80px; height: 80px; background: #ffffff; border-radius: 20px; margin: 0 auto 24px; box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.3); border: 2px solid #dbeafe;">
            <table role="presentation" width="100%" height="100%">
                <tr>
                    <td align="center" valign="middle">
                        <span style="font-size: 36px;">&#128737;</span>
                    </td>
                </tr>
            </table>
        </div>
        <h1 style="font-size: 28px; font-weight: 700; color: #111827; margin: 0 0 8px;">Payment Secured</h1>
        <p style="font-size: 16px; color: #6b7280; margin: 0;">Funds held safely in escrow</p>
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
        @if($isBuyer ?? false)
            Your payment has been successfully secured in our escrow system. The funds will be held safely until the work is completed and approved.
        @else
            Great news! The client's payment has been deposited into escrow. This means you can start working with confidence knowing the funds are secured.
        @endif
    </p>

    <!-- Amount Card -->
    <div class="amount-card">
        <div class="amount-header">
            <p style="font-size: 12px; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px; margin: 0 0 5px;">Amount in Escrow</p>
            <p style="font-size: 36px; color: white; font-weight: 800; margin: 0;">${{ number_format($escrow->amount, 2) }}</p>
        </div>
        <div style="padding: 20px;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px dashed #93c5fd;">
                        <span style="color: #6b7280; font-size: 14px;">Transaction ID</span>
                    </td>
                    <td style="padding: 12px 0; text-align: right; border-bottom: 1px dashed #93c5fd;">
                        <span style="color: #1e40af; font-weight: 600; font-family: monospace;">{{ $escrow->transaction_number }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px dashed #93c5fd;">
                        <span style="color: #6b7280; font-size: 14px;">Status</span>
                    </td>
                    <td style="padding: 12px 0; text-align: right; border-bottom: 1px dashed #93c5fd;">
                        <span style="display: inline-block; background: #dbeafe; color: #1e40af; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">Held</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 12px 0;">
                        <span style="color: #6b7280; font-size: 14px;">Held Date</span>
                    </td>
                    <td style="padding: 12px 0; text-align: right;">
                        <span style="color: #111827; font-weight: 600;">{{ $escrow->created_at->format('M d, Y') }}</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- How Escrow Works -->
    <div style="background: #f9fafb; border-radius: 16px; padding: 24px; margin-top: 24px;">
        <p style="font-size: 16px; font-weight: 700; color: #111827; margin: 0 0 24px; text-align: center;">How Escrow Protection Works</p>

        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="padding: 12px 0;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td style="vertical-align: top; padding-right: 15px;">
                                <div style="width: 36px; height: 36px; background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 50%; text-align: center; line-height: 36px; color: white; font-weight: 700;">1</div>
                            </td>
                            <td style="vertical-align: middle;">
                                <p style="font-size: 14px; font-weight: 600; color: #374151; margin: 0;">Payment Secured</p>
                                <p style="font-size: 13px; color: #6b7280; margin: 4px 0 0;">Funds are held safely in escrow</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="padding: 12px 0;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td style="vertical-align: top; padding-right: 15px;">
                                <div style="width: 36px; height: 36px; background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 50%; text-align: center; line-height: 36px; color: white; font-weight: 700;">2</div>
                            </td>
                            <td style="vertical-align: middle;">
                                <p style="font-size: 14px; font-weight: 600; color: #374151; margin: 0;">Work Completed</p>
                                <p style="font-size: 13px; color: #6b7280; margin: 4px 0 0;">Seller delivers the work as agreed</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="padding: 12px 0;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td style="vertical-align: top; padding-right: 15px;">
                                <div style="width: 36px; height: 36px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; text-align: center; line-height: 36px; color: white; font-weight: 700;">3</div>
                            </td>
                            <td style="vertical-align: middle;">
                                <p style="font-size: 14px; font-weight: 600; color: #374151; margin: 0;">Funds Released</p>
                                <p style="font-size: 13px; color: #6b7280; margin: 4px 0 0;">Payment released upon approval</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <!-- Security Note -->
    <div style="background: #fef3c7; border-radius: 12px; padding: 20px; margin-top: 24px; border-left: 4px solid #f59e0b;">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td style="vertical-align: top; padding-right: 12px;">
                    <span style="font-size: 20px;">&#128272;</span>
                </td>
                <td>
                    <p style="font-size: 14px; font-weight: 600; color: #92400e; margin: 0 0 4px;">Your Protection</p>
                    <p style="font-size: 14px; color: #b45309; margin: 0;">Both parties are protected by our escrow system. If any issues arise, our dispute resolution team is here to help.</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- CTA Button -->
    <div style="text-align: center; margin-top: 32px;">
        <a href="{{ url('/escrow/' . $escrow->id) }}" style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 30px; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);">
            View Escrow Details
        </a>
    </div>
</div>
@endsection
