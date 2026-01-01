@extends('emails.layouts.base')

@section('title', 'New Proposal Received - ' . $job->title)

@section('preview')
You received a new proposal for "{{ $job->title }}" from {{ $proposal->seller->user->name }}
@endsection

@section('additional_styles')
<style>
    .header-gradient {
        background: linear-gradient(135deg, #7c3aed 0%, #a855f7 50%, #c084fc 100%);
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
        background: url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M50 50c0-5.523 4.477-10 10-10s10 4.477 10 10-4.477 10-10 10c0 5.523-4.477 10-10 10s-10-4.477-10-10 4.477-10 10-10zM10 10c0-5.523 4.477-10 10-10s10 4.477 10 10-4.477 10-10 10c0 5.523-4.477 10-10 10S0 25.523 0 20s4.477-10 10-10z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .proposal-card {
        background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
        border: 1px solid #e9d5ff;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
    }
    .freelancer-section {
        display: flex;
        gap: 16px;
        align-items: flex-start;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid #e9d5ff;
    }
    .freelancer-avatar {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 24px;
        font-weight: 700;
        flex-shrink: 0;
    }
    .bid-amount {
        background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
        color: #ffffff;
        padding: 16px 24px;
        border-radius: 12px;
        text-align: center;
        margin-bottom: 16px;
    }
    .cover-letter {
        background: #ffffff;
        border: 1px solid #e9d5ff;
        border-radius: 12px;
        padding: 20px;
        margin-top: 20px;
    }
    .stats-grid {
        display: flex;
        gap: 12px;
        margin-top: 16px;
    }
    .stat-item {
        flex: 1;
        background: #ffffff;
        border: 1px solid #e9d5ff;
        border-radius: 8px;
        padding: 12px;
        text-align: center;
    }
</style>
@endsection

@section('hero')
<div style="background: #ffffff;">
    <div style="text-align: center; padding: 20px 40px 32px;">
        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); border-radius: 20px; margin: 0 auto 24px; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px -5px rgba(124, 58, 237, 0.4);">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M14 2V8H20M16 13H8M16 17H8M10 9H8" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h1 style="font-size: 28px; font-weight: 700; color: #111827; margin: 0 0 8px;">New Proposal Received!</h1>
        <p style="font-size: 16px; color: #6b7280; margin: 0;">Someone wants to work on your project</p>
    </div>
</div>
@endsection

@section('content')
<div class="email-content">
    <!-- Job Reference -->
    <div style="background: #f9fafb; border-radius: 12px; padding: 16px; margin-bottom: 24px;">
        <p style="color: #6b7280; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 4px;">Your Job Posting</p>
        <p style="color: #111827; font-weight: 600; font-size: 16px; margin: 0;">{{ $job->title }}</p>
    </div>

    <!-- Proposal Card -->
    <div class="proposal-card">
        <!-- Freelancer Info -->
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #e9d5ff;">
            <tr>
                <td style="width: 64px; vertical-align: top;">
                    <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; color: #ffffff; font-size: 24px; font-weight: 700;">
                        {{ strtoupper(substr($proposal->seller->user->name, 0, 1)) }}
                    </div>
                </td>
                <td style="padding-left: 16px; vertical-align: top;">
                    <p style="font-weight: 700; color: #111827; font-size: 18px; margin: 0 0 4px;">{{ $proposal->seller->user->name }}</p>
                    @if($proposal->seller)
                    <p style="color: #7c3aed; font-size: 14px; margin: 0 0 8px;">{{ $proposal->seller->title ?? 'Freelancer' }}</p>
                    @endif
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td style="padding-right: 16px;">
                                <span style="color: #fbbf24;">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                                <span style="color: #6b7280; font-size: 13px; margin-left: 4px;">{{ $proposal->seller->rating ?? '5.0' }}</span>
                            </td>
                            <td>
                                <span style="color: #6b7280; font-size: 13px;">{{ $proposal->seller->completed_orders ?? 0 }} jobs completed</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Bid Amount -->
        <div style="background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); color: #ffffff; padding: 16px 24px; border-radius: 12px; text-align: center; margin-bottom: 16px;">
            <p style="color: #ffffff; font-size: 12px; opacity: 0.9; margin: 0 0 4px; text-transform: uppercase; letter-spacing: 0.5px;">Proposed Amount</p>
            <p style="color: #ffffff; font-size: 32px; font-weight: 800; margin: 0;">{{ format_price($proposal->proposed_price ?? 0) }}</p>
            <p style="color: #ffffff; font-size: 14px; opacity: 0.9; margin: 8px 0 0;">{{ $proposal->duration_text ?? 'Flexible timeline' }}</p>
        </div>

        <!-- Stats -->
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="width: 50%; padding: 4px;">
                    <div style="background: #ffffff; border: 1px solid #e9d5ff; border-radius: 8px; padding: 12px; text-align: center;">
                        <p style="font-size: 20px; font-weight: 700; color: #7c3aed; margin: 0;">{{ $proposal->proposed_duration ?? '-' }}</p>
                        <p style="font-size: 11px; color: #6b7280; margin: 4px 0 0;">{{ ucfirst($proposal->duration_type ?? 'Days') }}</p>
                    </div>
                </td>
                <td style="width: 50%; padding: 4px;">
                    <div style="background: #ffffff; border: 1px solid #e9d5ff; border-radius: 8px; padding: 12px; text-align: center;">
                        <p style="font-size: 20px; font-weight: 700; color: #7c3aed; margin: 0;">{{ is_array($proposal->milestones) ? count($proposal->milestones) : 1 }}</p>
                        <p style="font-size: 11px; color: #6b7280; margin: 4px 0 0;">Milestones</p>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Cover Letter -->
        @if($proposal->cover_letter)
        <div style="background: #ffffff; border: 1px solid #e9d5ff; border-radius: 12px; padding: 20px; margin-top: 20px;">
            <p style="font-weight: 600; color: #5b21b6; font-size: 14px; margin: 0 0 12px;">Cover Letter</p>
            <p style="color: #374151; font-size: 14px; line-height: 1.6; margin: 0; white-space: pre-line;">{{ Str::limit($proposal->cover_letter, 300) }}</p>
        </div>
        @endif
    </div>

    <!-- CTA Buttons -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td align="center" style="padding: 24px 0;">
                <a href="{{ url('/jobs/' . $job->id . '/proposals/' . $proposal->id) }}" style="display: inline-block; padding: 16px 32px; background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 12px; box-shadow: 0 4px 14px -3px rgba(124, 58, 237, 0.5); margin-right: 12px;">
                    View Full Proposal
                </a>
                <a href="{{ url('/jobs/' . $job->id . '/proposals') }}" style="display: inline-block; padding: 16px 24px; background: #ffffff; color: #7c3aed; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 12px; border: 2px solid #e9d5ff;">
                    All Proposals
                </a>
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <!-- Help Text -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="text-align: center;">
                <p style="color: #6b7280; font-size: 14px; margin: 0;">Review proposals carefully and choose the best fit for your project.</p>
            </td>
        </tr>
    </table>
</div>
@endsection
