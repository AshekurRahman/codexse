@extends('emails.layouts.base')

@section('title', '[' . $alert->severity_name . '] Fraud Alert')

@section('preview')
[{{ strtoupper($alert->severity) }}] Fraud alert detected: {{ $alert->type_name }} - Risk Score {{ $alert->risk_score }}/100
@endsection

@section('additional_styles')
<style>
    .header-gradient-critical {
        background: linear-gradient(135deg, #DC2626 0%, #B91C1C 50%, #991B1B 100%);
    }
    .header-gradient-high {
        background: linear-gradient(135deg, #EF4444 0%, #DC2626 50%, #B91C1C 100%);
    }
    .header-gradient-medium {
        background: linear-gradient(135deg, #F59E0B 0%, #D97706 50%, #B45309 100%);
    }
    .header-gradient-low {
        background: linear-gradient(135deg, #3B82F6 0%, #2563EB 50%, #1D4ED8 100%);
    }
    .alert-card {
        border-radius: 16px;
        overflow: hidden;
        border: 2px solid;
    }
    .alert-card-critical {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        border-color: #fca5a5;
    }
    .alert-card-high {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        border-color: #fca5a5;
    }
    .alert-card-medium {
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        border-color: #fcd34d;
    }
    .alert-card-low {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border-color: #93c5fd;
    }
    .risk-meter {
        height: 12px;
        background: #e5e7eb;
        border-radius: 6px;
        overflow: hidden;
    }
    .risk-fill {
        height: 100%;
        border-radius: 6px;
    }
    .detail-grid {
        background: white;
        border-radius: 12px;
        padding: 20px;
    }
    .detail-row {
        padding: 10px 0;
        border-bottom: 1px solid #e5e7eb;
    }
    .detail-row:last-child {
        border-bottom: none;
    }
    .blocked-badge {
        display: inline-block;
        background: #dc2626;
        color: white;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }
</style>
@endsection

@php
    $severityClass = match($alert->severity) {
        'critical' => 'critical',
        'high' => 'high',
        'medium' => 'medium',
        default => 'low',
    };
    $severityColors = [
        'low' => '#3B82F6',
        'medium' => '#F59E0B',
        'high' => '#EF4444',
        'critical' => '#DC2626',
    ];
    $riskColor = $severityColors[$alert->severity] ?? '#3B82F6';
@endphp

@section('hero')
<div style="background: #ffffff;">
    <div style="text-align: center; padding: 20px 40px 32px;">
        <div style="width: 80px; height: 80px; background: {{ $riskColor }}20; border-radius: 20px; margin: 0 auto 24px; border: 2px solid {{ $riskColor }};">
            <table role="presentation" width="100%" height="100%">
                <tr>
                    <td align="center" valign="middle">
                        <span style="font-size: 36px;">&#9888;</span>
                    </td>
                </tr>
            </table>
        </div>
        <h1 style="font-size: 28px; font-weight: 700; color: #111827; margin: 0 0 8px;">Fraud Alert Detected</h1>
        <p style="font-size: 16px; color: #6b7280; margin: 0;">
            <span style="display: inline-block; background: {{ $riskColor }}; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase;">{{ $alert->severity_name }}</span>
        </p>
    </div>
</div>
@endsection

@section('content')
<div style="padding: 0 40px 40px;">
    <!-- Alert Summary -->
    <p style="font-size: 16px; color: #6b7280; margin: 0 0 24px;">
        A suspicious transaction has been flagged by our fraud detection system. Please review the details below and take appropriate action.
    </p>

    <!-- Alert Card -->
    <div class="alert-card alert-card-{{ $severityClass }}">
        <div style="padding: 20px; background: {{ $riskColor }}; text-align: center;">
            <p style="font-size: 12px; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px; margin: 0 0 5px;">Alert Number</p>
            <p style="font-size: 18px; color: white; font-weight: 700; margin: 0; font-family: monospace;">{{ $alert->alert_number }}</p>
        </div>
        <div style="padding: 20px;">
            <!-- Risk Score -->
            <div style="margin-bottom: 20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td>
                            <span style="font-size: 14px; font-weight: 600; color: #374151;">Risk Score</span>
                        </td>
                        <td style="text-align: right;">
                            <span style="font-size: 24px; font-weight: 800; color: {{ $riskColor }};">{{ $alert->risk_score }}</span>
                            <span style="font-size: 14px; color: #6b7280;">/100</span>
                        </td>
                    </tr>
                </table>
                <div class="risk-meter" style="margin-top: 10px;">
                    <div class="risk-fill" style="width: {{ $alert->risk_score }}%; background: linear-gradient(90deg, #10b981, #f59e0b, #ef4444);"></div>
                </div>
            </div>

            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px dashed #e5e7eb;">
                        <span style="color: #6b7280; font-size: 14px;">Alert Type</span>
                    </td>
                    <td style="padding: 12px 0; text-align: right; border-bottom: 1px dashed #e5e7eb;">
                        <span style="color: #111827; font-weight: 600;">{{ $alert->type_name }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px dashed #e5e7eb;">
                        <span style="color: #6b7280; font-size: 14px;">Amount</span>
                    </td>
                    <td style="padding: 12px 0; text-align: right; border-bottom: 1px dashed #e5e7eb;">
                        <span style="color: #111827; font-weight: 700; font-size: 18px;">{{ $alert->formatted_amount }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px dashed #e5e7eb;">
                        <span style="color: #6b7280; font-size: 14px;">User</span>
                    </td>
                    <td style="padding: 12px 0; text-align: right; border-bottom: 1px dashed #e5e7eb;">
                        <span style="color: #111827; font-weight: 600;">{{ $alert->user?->name ?? 'Guest' }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px dashed #e5e7eb;">
                        <span style="color: #6b7280; font-size: 14px;">IP Address</span>
                    </td>
                    <td style="padding: 12px 0; text-align: right; border-bottom: 1px dashed #e5e7eb;">
                        <span style="color: #111827; font-weight: 600; font-family: monospace;">{{ $alert->ip_address }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 12px 0;">
                        <span style="color: #6b7280; font-size: 14px;">Detected At</span>
                    </td>
                    <td style="padding: 12px 0; text-align: right;">
                        <span style="color: #111827; font-weight: 600;">{{ $alert->created_at->format('M d, Y - h:i A') }}</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    @if($alert->auto_blocked)
    <!-- Auto Blocked Notice -->
    <div style="background: #fef2f2; border-radius: 12px; padding: 20px; margin-top: 24px; border-left: 4px solid #dc2626; text-align: center;">
        <span class="blocked-badge">&#128683; Transaction Blocked</span>
        <p style="font-size: 14px; color: #991b1b; margin: 12px 0 0;">This transaction was automatically blocked due to high risk score.</p>
    </div>
    @endif

    <!-- Flags Detected -->
    @if($alert->flags ?? null)
    <div style="background: #f9fafb; border-radius: 16px; padding: 24px; margin-top: 24px;">
        <p style="font-size: 14px; font-weight: 600; color: #374151; margin: 0 0 16px;">&#128681; Flags Detected</p>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            @foreach($alert->flags as $flag)
            <tr>
                <td style="padding: 8px 0;">
                    <span style="color: {{ $riskColor }}; margin-right: 8px;">&#9679;</span>
                    <span style="color: #6b7280; font-size: 14px;">{{ $flag }}</span>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif

    <!-- Action Required -->
    <div style="background: #fffbeb; border-radius: 12px; padding: 20px; margin-top: 24px; border-left: 4px solid #f59e0b;">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td style="vertical-align: top; padding-right: 12px;">
                    <span style="font-size: 20px;">&#9888;</span>
                </td>
                <td>
                    <p style="font-size: 14px; font-weight: 600; color: #92400e; margin: 0 0 4px;">Action Required</p>
                    <p style="font-size: 14px; color: #b45309; margin: 0;">Please review this alert immediately and take appropriate action to protect the platform and users.</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- CTA Button -->
    <div style="text-align: center; margin-top: 32px;">
        <a href="{{ url('/admin/fraud-alerts/' . $alert->id) }}" style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, {{ $riskColor }} 0%, {{ $riskColor }}dd 100%); color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 30px; box-shadow: 0 4px 15px {{ $riskColor }}66;">
            Review Alert
        </a>
    </div>
</div>
@endsection
