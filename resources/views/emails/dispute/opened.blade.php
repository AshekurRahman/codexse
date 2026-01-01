@extends('emails.layouts.base')

@section('title', 'Dispute ' . ($isInitiator ? 'Submitted' : 'Filed'))

@section('preview')
@if($isInitiator ?? false)
Your dispute has been submitted successfully. Our team will review your case.
@else
A dispute has been filed regarding your transaction. Please review and respond.
@endif
@endsection

@section('additional_styles')
<style>
    .header-gradient {
        background: linear-gradient(135deg, #F59E0B 0%, #D97706 50%, #B45309 100%);
        position: relative;
        overflow: hidden;
    }
    .dispute-card {
        background: #fef3c7;
        border: 1px solid #fcd34d;
        border-radius: 16px;
        overflow: hidden;
    }
    .dispute-header {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        padding: 20px;
    }
    .process-step {
        background: white;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 12px;
    }
    .process-step:last-child {
        margin-bottom: 0;
    }
    .action-required {
        background: #fee2e2;
        border-radius: 12px;
        padding: 20px;
        border-left: 4px solid #ef4444;
    }
</style>
@endsection

@section('hero')
<div style="background: #ffffff;">
    <div style="text-align: center; padding: 20px 40px 32px;">
        <div style="width: 80px; height: 80px; background: #ffffff; border-radius: 20px; margin: 0 auto 24px; box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.3); border: 2px solid #fef3c7;">
            <table role="presentation" width="100%" height="100%">
                <tr>
                    <td align="center" valign="middle">
                        <span style="font-size: 36px;">&#9888;</span>
                    </td>
                </tr>
            </table>
        </div>
        <h1 style="font-size: 28px; font-weight: 700; color: #111827; margin: 0 0 8px;">
            @if($isInitiator ?? false)
                Dispute Submitted
            @else
                Dispute Filed
            @endif
        </h1>
        <p style="font-size: 16px; color: #6b7280; margin: 0;">Our team will review your case</p>
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
        @if($isInitiator ?? false)
            Your dispute has been submitted successfully. Our resolution team will carefully review your case and work to find a fair solution for all parties involved.
        @else
            A dispute has been filed regarding a transaction you're involved in. Please review the details below and provide your response to help us resolve this matter fairly.
        @endif
    </p>

    <!-- Dispute Card -->
    <div class="dispute-card">
        <div class="dispute-header">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td>
                        <p style="font-size: 12px; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px; margin: 0 0 5px;">Dispute ID</p>
                        <p style="font-size: 18px; color: white; font-weight: 700; margin: 0;">#{{ $dispute->id }}</p>
                    </td>
                    <td style="text-align: right;">
                        <span style="display: inline-block; background: rgba(255,255,255,0.2); color: white; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase;">{{ $dispute->status ?? 'Open' }}</span>
                    </td>
                </tr>
            </table>
        </div>
        <div style="padding: 20px;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px dashed #fcd34d;">
                        <span style="color: #92400e; font-size: 14px;">Reason</span>
                    </td>
                    <td style="padding: 12px 0; text-align: right; border-bottom: 1px dashed #fcd34d;">
                        <span style="color: #78350f; font-weight: 600;">{{ ucfirst(str_replace('_', ' ', $dispute->reason)) }}</span>
                    </td>
                </tr>
                @if($dispute->escrowTransaction ?? null)
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px dashed #fcd34d;">
                        <span style="color: #92400e; font-size: 14px;">Amount</span>
                    </td>
                    <td style="padding: 12px 0; text-align: right; border-bottom: 1px dashed #fcd34d;">
                        <span style="color: #78350f; font-weight: 700; font-size: 18px;">${{ number_format($dispute->escrowTransaction->amount ?? 0, 2) }}</span>
                    </td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px dashed #fcd34d;">
                        <span style="color: #92400e; font-size: 14px;">Filed On</span>
                    </td>
                    <td style="padding: 12px 0; text-align: right; border-bottom: 1px dashed #fcd34d;">
                        <span style="color: #78350f; font-weight: 600;">{{ $dispute->created_at->format('M d, Y - h:i A') }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 12px 0;">
                        <span style="color: #92400e; font-size: 14px;">Filed By</span>
                    </td>
                    <td style="padding: 12px 0; text-align: right;">
                        <span style="color: #78350f; font-weight: 600;">{{ $dispute->initiator->name ?? 'User' }}</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    @if($dispute->description ?? null)
    <!-- Description -->
    <div style="background: #f3f4f6; border-radius: 12px; padding: 20px; margin-top: 24px;">
        <p style="font-size: 14px; font-weight: 600; color: #374151; margin: 0 0 10px;">Description</p>
        <p style="font-size: 14px; color: #6b7280; margin: 0; line-height: 1.7;">{{ $dispute->description }}</p>
    </div>
    @endif

    <!-- What Happens Next -->
    <div style="background: #eff6ff; border-radius: 16px; padding: 24px; margin-top: 24px; border: 1px solid #bfdbfe;">
        <p style="font-size: 16px; font-weight: 700; color: #1e40af; margin: 0 0 20px;">What Happens Next?</p>

        <div class="process-step">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td style="vertical-align: middle; padding-right: 12px;">
                        <span style="display: inline-block; width: 28px; height: 28px; background: #3b82f6; color: white; border-radius: 50%; text-align: center; line-height: 28px; font-weight: 700; font-size: 14px;">1</span>
                    </td>
                    <td>
                        <p style="font-size: 14px; color: #374151; margin: 0;"><strong>Review</strong> - Our team examines all evidence</p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="process-step">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td style="vertical-align: middle; padding-right: 12px;">
                        <span style="display: inline-block; width: 28px; height: 28px; background: #3b82f6; color: white; border-radius: 50%; text-align: center; line-height: 28px; font-weight: 700; font-size: 14px;">2</span>
                    </td>
                    <td>
                        <p style="font-size: 14px; color: #374151; margin: 0;"><strong>Response</strong> - Both parties provide input</p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="process-step">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td style="vertical-align: middle; padding-right: 12px;">
                        <span style="display: inline-block; width: 28px; height: 28px; background: #10b981; color: white; border-radius: 50%; text-align: center; line-height: 28px; font-weight: 700; font-size: 14px;">3</span>
                    </td>
                    <td>
                        <p style="font-size: 14px; color: #374151; margin: 0;"><strong>Resolution</strong> - Fair decision made within 7 days</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    @if(!($isInitiator ?? false))
    <!-- Action Required -->
    <div class="action-required" style="margin-top: 24px;">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td style="vertical-align: top; padding-right: 12px;">
                    <span style="font-size: 20px;">&#9200;</span>
                </td>
                <td>
                    <p style="font-size: 14px; font-weight: 600; color: #991b1b; margin: 0 0 4px;">Action Required</p>
                    <p style="font-size: 14px; color: #b91c1c; margin: 0;">Please respond within 72 hours to provide your side of the story and any supporting evidence.</p>
                </td>
            </tr>
        </table>
    </div>
    @endif

    <!-- CTA Button -->
    <div style="text-align: center; margin-top: 32px;">
        <a href="{{ url('/disputes/' . $dispute->id) }}" style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 30px; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);">
            @if($isInitiator ?? false)
                Track Dispute
            @else
                Respond to Dispute
            @endif
        </a>
    </div>
</div>
@endsection
