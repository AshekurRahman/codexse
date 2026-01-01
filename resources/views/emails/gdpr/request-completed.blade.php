@extends('emails.layouts.base')

@section('title', 'Your ' . $request->type_name . ' Request is Complete')

@section('preview')
Your {{ $request->type_name }} request ({{ $request->request_number }}) has been completed successfully.
@endsection

@section('additional_styles')
<style>
    .header-gradient {
        background: linear-gradient(135deg, #10B981 0%, #059669 50%, #047857 100%);
        position: relative;
        overflow: hidden;
    }
    .completion-card {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        border: 1px solid #a7f3d0;
        border-radius: 16px;
        overflow: hidden;
    }
    .completion-header {
        background: linear-gradient(135deg, #10b981, #059669);
        padding: 20px;
        text-align: center;
    }
    .download-section {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border-radius: 12px;
        padding: 24px;
        text-align: center;
        border: 1px solid #93c5fd;
    }
    .data-list {
        background: #f9fafb;
        border-radius: 10px;
        padding: 16px;
    }
    .data-item {
        padding: 8px 0;
        border-bottom: 1px solid #e5e7eb;
        color: #6b7280;
        font-size: 14px;
    }
    .data-item:last-child {
        border-bottom: none;
    }
</style>
@endsection

@section('hero')
<div style="background: #ffffff;">
    <div style="text-align: center; padding: 20px 40px 32px;">
        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 20px; margin: 0 auto 24px; box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4);">
            <table role="presentation" width="100%" height="100%">
                <tr>
                    <td align="center" valign="middle">
                        <span style="font-size: 36px;">&#10003;</span>
                    </td>
                </tr>
            </table>
        </div>
        <h1 style="font-size: 28px; font-weight: 700; color: #111827; margin: 0 0 8px;">Request Complete</h1>
        <p style="font-size: 16px; color: #6b7280; margin: 0;">Your {{ $request->type_name }} request has been processed</p>
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
        Your <strong>{{ $request->type_name }}</strong> request (Reference: {{ $request->request_number }}) has been completed successfully.
    </p>

    <!-- Completion Card -->
    <div class="completion-card">
        <div class="completion-header">
            <p style="font-size: 12px; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px; margin: 0 0 5px;">Status</p>
            <p style="font-size: 20px; color: white; font-weight: 700; margin: 0;">Completed</p>
        </div>
        <div style="padding: 20px;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px dashed #a7f3d0;">
                        <span style="color: #6b7280; font-size: 14px;">Reference</span>
                    </td>
                    <td style="padding: 12px 0; text-align: right; border-bottom: 1px dashed #a7f3d0;">
                        <span style="color: #047857; font-weight: 600; font-family: monospace;">{{ $request->request_number }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px dashed #a7f3d0;">
                        <span style="color: #6b7280; font-size: 14px;">Request Type</span>
                    </td>
                    <td style="padding: 12px 0; text-align: right; border-bottom: 1px dashed #a7f3d0;">
                        <span style="color: #047857; font-weight: 600;">{{ $request->type_name }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 12px 0;">
                        <span style="color: #6b7280; font-size: 14px;">Completed On</span>
                    </td>
                    <td style="padding: 12px 0; text-align: right;">
                        <span style="color: #111827; font-weight: 600;">{{ now()->format('M d, Y') }}</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    @if($request->type === 'export')
    <!-- Download Section for Export -->
    <div class="download-section" style="margin-top: 24px;">
        <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 14px; margin: 0 auto 16px;">
            <table role="presentation" width="100%" height="100%">
                <tr>
                    <td align="center" valign="middle">
                        <span style="font-size: 28px;">&#128229;</span>
                    </td>
                </tr>
            </table>
        </div>
        <h3 style="font-size: 18px; font-weight: 600; color: #1e40af; margin: 0 0 8px;">Your Data is Ready</h3>
        <p style="font-size: 14px; color: #3b82f6; margin: 0 0 16px;">Download link available for 7 days</p>

        <a href="{{ route('gdpr.download', $request->id ?? 1) }}" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 30px; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);">
            Download Your Data
        </a>

        <!-- Data Contents -->
        <div class="data-list" style="margin-top: 20px; text-align: left;">
            <p style="font-size: 13px; font-weight: 600; color: #374151; margin: 0 0 12px;">The download contains:</p>
            <div class="data-item">&#10003; Your personal information</div>
            <div class="data-item">&#10003; Order history and transactions</div>
            <div class="data-item">&#10003; Reviews and communications</div>
            <div class="data-item">&#10003; All other data we hold about you</div>
        </div>
    </div>
    @else
    <!-- General Completion Message -->
    <div style="background: #f9fafb; border-radius: 16px; padding: 24px; margin-top: 24px; text-align: center;">
        <p style="font-size: 16px; color: #374151; margin: 0 0 8px;">Your request has been successfully processed.</p>
        <p style="font-size: 14px; color: #6b7280; margin: 0;">If you have any questions about the outcome, please contact our support team.</p>
    </div>

    <!-- CTA Button -->
    <div style="text-align: center; margin-top: 24px;">
        <a href="{{ route('gdpr.requests') }}" style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 30px; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);">
            View Request Details
        </a>
    </div>
    @endif

    <!-- Privacy Note -->
    <div style="background: #eff6ff; border-radius: 12px; padding: 20px; margin-top: 24px; border-left: 4px solid #3b82f6;">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td style="vertical-align: top; padding-right: 12px;">
                    <span style="font-size: 20px;">&#128274;</span>
                </td>
                <td>
                    <p style="font-size: 14px; font-weight: 600; color: #1e40af; margin: 0 0 4px;">Your Privacy Matters</p>
                    <p style="font-size: 14px; color: #3b82f6; margin: 0;">We take data protection seriously and are committed to handling your personal information responsibly.</p>
                </td>
            </tr>
        </table>
    </div>
</div>
@endsection
