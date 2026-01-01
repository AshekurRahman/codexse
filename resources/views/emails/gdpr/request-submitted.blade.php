@extends('emails.layouts.base')

@section('title', 'Your ' . $request->type_name . ' Request Received')

@section('preview')
Your {{ $request->type_name }} request ({{ $request->request_number }}) has been received and is being processed.
@endsection

@section('additional_styles')
<style>
    .header-gradient {
        background: linear-gradient(135deg, #6366F1 0%, #4F46E5 50%, #4338CA 100%);
        position: relative;
        overflow: hidden;
    }
    .request-card {
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        border: 1px solid #c7d2fe;
        border-radius: 16px;
        overflow: hidden;
    }
    .request-header {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        padding: 20px;
        text-align: center;
    }
    .timeline-step {
        padding: 16px 20px;
        background: white;
        border-radius: 10px;
        margin-bottom: 12px;
        border-left: 4px solid #c7d2fe;
    }
    .timeline-step.active {
        border-left-color: #6366f1;
        background: #f5f3ff;
    }
    .security-notice {
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
        <div style="width: 80px; height: 80px; background: #ffffff; border-radius: 20px; margin: 0 auto 24px; box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.3); border: 2px solid #e0e7ff;">
            <table role="presentation" width="100%" height="100%">
                <tr>
                    <td align="center" valign="middle">
                        <span style="font-size: 36px;">&#128274;</span>
                    </td>
                </tr>
            </table>
        </div>
        <h1 style="font-size: 28px; font-weight: 700; color: #111827; margin: 0 0 8px;">Request Received</h1>
        <p style="font-size: 16px; color: #6b7280; margin: 0;">We're processing your {{ $request->type_name }} request</p>
    </div>
</div>
@endsection

@section('content')
<div style="padding: 0 40px 40px;">
    <!-- Greeting -->
    <p style="font-size: 16px; color: #374151; margin: 0 0 16px;">
        Hello {{ $user->name }},
    </p>
    <p style="font-size: 16px; color: #6b7280; margin: 0 0 24px;">
        We have received your <strong>{{ $request->type_name }}</strong> request. Under GDPR regulations, we are required to process your request within 30 days.
    </p>

    <!-- Request Card -->
    <div class="request-card">
        <div class="request-header">
            <p style="font-size: 12px; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px; margin: 0 0 5px;">Reference Number</p>
            <p style="font-size: 20px; color: white; font-weight: 700; margin: 0; font-family: monospace;">{{ $request->request_number }}</p>
        </div>
        <div style="padding: 20px;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px dashed #c7d2fe;">
                        <span style="color: #6b7280; font-size: 14px;">Request Type</span>
                    </td>
                    <td style="padding: 12px 0; text-align: right; border-bottom: 1px dashed #c7d2fe;">
                        <span style="color: #4338ca; font-weight: 600;">{{ $request->type_name }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px dashed #c7d2fe;">
                        <span style="color: #6b7280; font-size: 14px;">Submitted</span>
                    </td>
                    <td style="padding: 12px 0; text-align: right; border-bottom: 1px dashed #c7d2fe;">
                        <span style="color: #111827; font-weight: 600;">{{ $request->created_at->format('M d, Y') }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 12px 0;">
                        <span style="color: #6b7280; font-size: 14px;">Status</span>
                    </td>
                    <td style="padding: 12px 0; text-align: right;">
                        <span style="display: inline-block; background: #fef3c7; color: #92400e; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">Pending</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Process Timeline -->
    <div style="background: #f9fafb; border-radius: 16px; padding: 24px; margin-top: 24px;">
        <p style="font-size: 16px; font-weight: 700; color: #111827; margin: 0 0 20px;">What Happens Next?</p>

        <div class="timeline-step active">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td style="vertical-align: middle; padding-right: 12px;">
                        <span style="display: inline-block; width: 28px; height: 28px; background: #6366f1; color: white; border-radius: 50%; text-align: center; line-height: 28px; font-weight: 700; font-size: 14px;">1</span>
                    </td>
                    <td>
                        <p style="font-size: 14px; font-weight: 600; color: #4338ca; margin: 0;">Request Received</p>
                        <p style="font-size: 13px; color: #6b7280; margin: 4px 0 0;">Your request has been logged</p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="timeline-step">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td style="vertical-align: middle; padding-right: 12px;">
                        <span style="display: inline-block; width: 28px; height: 28px; background: #e5e7eb; color: #6b7280; border-radius: 50%; text-align: center; line-height: 28px; font-weight: 700; font-size: 14px;">2</span>
                    </td>
                    <td>
                        <p style="font-size: 14px; font-weight: 600; color: #374151; margin: 0;">Identity Verification</p>
                        <p style="font-size: 13px; color: #6b7280; margin: 4px 0 0;">We verify your identity for security</p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="timeline-step">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td style="vertical-align: middle; padding-right: 12px;">
                        <span style="display: inline-block; width: 28px; height: 28px; background: #e5e7eb; color: #6b7280; border-radius: 50%; text-align: center; line-height: 28px; font-weight: 700; font-size: 14px;">3</span>
                    </td>
                    <td>
                        <p style="font-size: 14px; font-weight: 600; color: #374151; margin: 0;">Processing</p>
                        <p style="font-size: 13px; color: #6b7280; margin: 4px 0 0;">We process your request</p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="timeline-step" style="margin-bottom: 0;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td style="vertical-align: middle; padding-right: 12px;">
                        <span style="display: inline-block; width: 28px; height: 28px; background: #e5e7eb; color: #6b7280; border-radius: 50%; text-align: center; line-height: 28px; font-weight: 700; font-size: 14px;">4</span>
                    </td>
                    <td>
                        <p style="font-size: 14px; font-weight: 600; color: #374151; margin: 0;">Completion</p>
                        <p style="font-size: 13px; color: #6b7280; margin: 4px 0 0;">You'll be notified when complete</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Security Notice -->
    <div class="security-notice" style="margin-top: 24px;">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td style="vertical-align: top; padding-right: 12px;">
                    <span style="font-size: 20px;">&#9888;</span>
                </td>
                <td>
                    <p style="font-size: 14px; font-weight: 600; color: #92400e; margin: 0 0 4px;">Didn't make this request?</p>
                    <p style="font-size: 14px; color: #b45309; margin: 0;">If you did not submit this request, please contact our support team immediately as your account may be compromised.</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- CTA Button -->
    <div style="text-align: center; margin-top: 32px;">
        <a href="{{ route('gdpr.requests') }}" style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 30px; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);">
            View Request Status
        </a>
    </div>
</div>
@endsection
