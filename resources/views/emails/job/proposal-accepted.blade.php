@extends('emails.layouts.base')

@section('title', 'Proposal Accepted!')

@section('preview')
Congratulations! Your proposal for "{{ $job->title }}" has been accepted.
@endsection

@section('additional_styles')
<style>
    .header-gradient {
        background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 50%, #6D28D9 100%);
        position: relative;
        overflow: hidden;
    }
    .celebration-emoji {
        position: absolute;
        font-size: 24px;
    }
    .project-card {
        background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
        border: 1px solid #c4b5fd;
        border-radius: 16px;
        overflow: hidden;
    }
    .escrow-notice {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        border-radius: 12px;
        padding: 20px;
        border-left: 4px solid #10b981;
    }
    .next-step {
        padding: 12px 0;
        padding-left: 20px;
        border-left: 3px solid #8b5cf6;
        margin-bottom: 16px;
    }
</style>
@endsection

@section('hero')
<div style="background: #ffffff;">
    <div style="text-align: center; padding: 20px 40px 32px;">
        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #fcd34d, #f59e0b); border-radius: 20px; margin: 0 auto 24px; box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.4);">
            <table role="presentation" width="100%" height="100%">
                <tr>
                    <td align="center" valign="middle">
                        <span style="font-size: 36px;">&#127942;</span>
                    </td>
                </tr>
            </table>
        </div>
        <h1 style="font-size: 28px; font-weight: 700; color: #111827; margin: 0 0 8px;">Proposal Accepted!</h1>
        <p style="font-size: 16px; color: #6b7280; margin: 0;">You got the project!</p>
    </div>
</div>
@endsection

@section('content')
<div style="padding: 0 40px 40px;">
    <!-- Greeting -->
    <p style="font-size: 16px; color: #374151; margin: 0 0 16px;">
        Congratulations {{ $freelancer->name }}! &#127881;
    </p>
    <p style="font-size: 16px; color: #6b7280; margin: 0 0 24px;">
        Great news! Your proposal for <strong>"{{ $job->title }}"</strong> has been accepted. A contract has been created and you can start working on the project right away.
    </p>

    <!-- Project Card -->
    <div class="project-card">
        <div style="padding: 20px; border-bottom: 1px solid #c4b5fd;">
            <p style="font-size: 12px; color: #7c3aed; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 8px;">Project</p>
            <p style="font-size: 18px; color: #4c1d95; font-weight: 700; margin: 0;">{{ $job->title }}</p>
        </div>
        <div style="padding: 20px;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px dashed #c4b5fd;">
                        <span style="color: #7c3aed; font-size: 14px;">&#128188; Client</span>
                    </td>
                    <td style="padding: 10px 0; text-align: right; border-bottom: 1px dashed #c4b5fd;">
                        <span style="color: #4c1d95; font-weight: 600;">{{ $job->client->name ?? 'Client' }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px dashed #c4b5fd;">
                        <span style="color: #7c3aed; font-size: 14px;">&#128176; Project Amount</span>
                    </td>
                    <td style="padding: 10px 0; text-align: right; border-bottom: 1px dashed #c4b5fd;">
                        <span style="color: #4c1d95; font-weight: 700; font-size: 18px;">${{ number_format($proposal->proposed_price, 2) }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px dashed #c4b5fd;">
                        <span style="color: #7c3aed; font-size: 14px;">&#128197; Delivery Time</span>
                    </td>
                    <td style="padding: 10px 0; text-align: right; border-bottom: 1px dashed #c4b5fd;">
                        <span style="color: #4c1d95; font-weight: 600;">{{ $proposal->delivery_days ?? $job->deadline_days ?? 'TBD' }} days</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px 0;">
                        <span style="color: #7c3aed; font-size: 14px;">&#128205; Category</span>
                    </td>
                    <td style="padding: 10px 0; text-align: right;">
                        <span style="color: #4c1d95; font-weight: 600;">{{ $job->category->name ?? 'General' }}</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Escrow Notice -->
    <div class="escrow-notice" style="margin-top: 24px;">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td style="vertical-align: top; padding-right: 15px;">
                    <span style="font-size: 24px;">&#128274;</span>
                </td>
                <td>
                    <p style="font-size: 14px; font-weight: 600; color: #047857; margin: 0 0 5px;">Funds Secured in Escrow</p>
                    <p style="font-size: 14px; color: #059669; margin: 0;">The client's payment has been deposited into escrow. Once you complete the work and the client approves, the funds will be released to your wallet.</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- What's Next -->
    <div style="background: #f9fafb; border-radius: 16px; padding: 24px; margin-top: 24px;">
        <p style="font-size: 16px; font-weight: 700; color: #111827; margin: 0 0 20px;">&#128640; What's Next?</p>

        <div class="next-step">
            <p style="font-size: 14px; font-weight: 600; color: #374151; margin: 0 0 4px;">1. Review the Contract</p>
            <p style="font-size: 14px; color: #6b7280; margin: 0;">Check the milestones and requirements carefully</p>
        </div>

        <div class="next-step">
            <p style="font-size: 14px; font-weight: 600; color: #374151; margin: 0 0 4px;">2. Start Working</p>
            <p style="font-size: 14px; color: #6b7280; margin: 0;">Begin on your first milestone right away</p>
        </div>

        <div class="next-step" style="margin-bottom: 0;">
            <p style="font-size: 14px; font-weight: 600; color: #374151; margin: 0 0 4px;">3. Communicate</p>
            <p style="font-size: 14px; color: #6b7280; margin: 0;">Keep your client updated on progress</p>
        </div>
    </div>

    <!-- CTA Button -->
    <div style="text-align: center; margin-top: 32px;">
        <a href="{{ url('/seller/contracts') }}" style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 30px; box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4);">
            View Contract
        </a>
    </div>
</div>
@endsection
