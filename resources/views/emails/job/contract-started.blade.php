@extends('emails.layouts.base')

@section('title', 'Contract Started - ' . $contract->title)

@section('preview')
Your contract "{{ $contract->title }}" has been started. Let's build something amazing!
@endsection

@section('additional_styles')
<style>
    .header-gradient {
        background: linear-gradient(135deg, #059669 0%, #10b981 50%, #34d399 100%);
        position: relative;
        overflow: hidden;
    }
    .contract-badge {
        display: inline-block;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .party-card {
        background: #f9fafb;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 12px;
    }
    .milestone-item {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 8px;
    }
    .milestone-number {
        width: 28px;
        height: 28px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 12px;
        font-weight: 700;
    }
</style>
@endsection

@section('hero')
<div style="background: #ffffff;">
    <div style="text-align: center; padding: 20px 40px 32px;">
        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 20px; margin: 0 auto 24px; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4);">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#ffffff" stroke-width="2"/>
                <path d="M7.5 12L10.5 15L16.5 9" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <span class="contract-badge">Contract Active</span>
        <h1 style="font-size: 26px; font-weight: 700; color: #111827; margin: 16px 0 8px;">{{ $contract->title }}</h1>
        <p style="font-size: 16px; color: #6b7280; margin: 0;">Your project is now officially underway!</p>
    </div>
</div>
@endsection

@section('content')
<div class="email-content">
    <!-- Contract Overview -->
    <div class="info-card">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                    <span style="color: #6b7280; font-size: 14px;">Contract ID</span>
                </td>
                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; text-align: right;">
                    <span style="color: #111827; font-weight: 600; font-size: 14px;">#{{ $contract->id }}</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                    <span style="color: #6b7280; font-size: 14px;">Total Value</span>
                </td>
                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; text-align: right;">
                    <span style="color: #10b981; font-weight: 700; font-size: 16px;">{{ format_price($contract->total_amount ?? 0) }}</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                    <span style="color: #6b7280; font-size: 14px;">Start Date</span>
                </td>
                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; text-align: right;">
                    <span style="color: #111827; font-weight: 600; font-size: 14px;">{{ $contract->created_at->format('F j, Y') }}</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 12px 0;">
                    <span style="color: #6b7280; font-size: 14px;">Expected Completion</span>
                </td>
                <td style="padding: 12px 0; text-align: right;">
                    <span style="color: #111827; font-weight: 600; font-size: 14px;">{{ $contract->deadline ? $contract->deadline->format('F j, Y') : 'TBD' }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Parties -->
    <h3 style="font-size: 14px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin: 24px 0 16px;">Contract Parties</h3>

    <div class="party-card">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="width: 48px; vertical-align: top;">
                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #ffffff; font-size: 18px; font-weight: 700;">
                        {{ strtoupper(substr($client->name ?? 'C', 0, 1)) }}
                    </div>
                </td>
                <td style="padding-left: 12px;">
                    <p style="font-size: 12px; color: #6b7280; margin: 0;">Client</p>
                    <p style="font-weight: 600; color: #111827; margin: 4px 0 0;">{{ $client->name ?? 'Client' }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="party-card">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="width: 48px; vertical-align: top;">
                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #ffffff; font-size: 18px; font-weight: 700;">
                        {{ strtoupper(substr($freelancer->name ?? 'F', 0, 1)) }}
                    </div>
                </td>
                <td style="padding-left: 12px;">
                    <p style="font-size: 12px; color: #6b7280; margin: 0;">Freelancer</p>
                    <p style="font-weight: 600; color: #111827; margin: 4px 0 0;">{{ $freelancer->name ?? 'Freelancer' }}</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Milestones -->
    @if($contract->milestones && $contract->milestones->count() > 0)
    <h3 style="font-size: 14px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin: 24px 0 16px;">Milestones</h3>

    @foreach($contract->milestones as $index => $milestone)
    <div class="milestone-item">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="width: 40px; vertical-align: top;">
                    <span class="milestone-number">{{ $index + 1 }}</span>
                </td>
                <td style="padding-left: 12px;">
                    <p style="font-weight: 600; color: #111827; margin: 0; font-size: 14px;">{{ $milestone->title }}</p>
                    <p style="color: #6b7280; margin: 4px 0 0; font-size: 13px;">Due: {{ $milestone->due_date ? $milestone->due_date->format('M j, Y') : 'TBD' }}</p>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <span style="font-weight: 600; color: #10b981;">{{ format_price($milestone->amount ?? 0) }}</span>
                </td>
            </tr>
        </table>
    </div>
    @endforeach
    @endif

    <!-- Escrow Info -->
    <div style="background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); border: 1px solid #a7f3d0; border-radius: 12px; padding: 20px; margin-top: 24px;">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="width: 48px; vertical-align: top;">
                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#ffffff" stroke-width="2"/>
                            <path d="M9 12L11 14L15 10" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </td>
                <td style="padding-left: 16px;">
                    <p style="font-weight: 600; color: #065f46; margin: 0 0 4px; font-size: 15px;">Funds Secured in Escrow</p>
                    <p style="color: #047857; margin: 0; font-size: 13px;">Payment is protected until each milestone is completed and approved.</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- CTA -->
    <div style="text-align: center; margin: 32px 0;">
        <a href="{{ url('/contracts/' . $contract->id) }}" style="display: inline-block; padding: 16px 32px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 12px; box-shadow: 0 4px 14px -3px rgba(16, 185, 129, 0.5);">
            View Contract Details
        </a>
    </div>

    <div class="divider"></div>

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="text-align: center;">
                <p style="color: #6b7280; font-size: 14px; margin: 0;">Good luck with your project! We're here if you need help.</p>
            </td>
        </tr>
    </table>
</div>
@endsection
