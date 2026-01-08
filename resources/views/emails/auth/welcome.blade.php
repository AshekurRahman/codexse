@extends('emails.layouts.base')

@section('title', 'Welcome to ' . config('app.name'))

@section('preview')
Welcome to {{ config('app.name') }}, {{ $user->name }}! Your account is ready.
@endsection

@section('hero')
<div style="background: #ffffff; padding: 40px; text-align: center;">
    <!-- Welcome Icon -->
    <div style="width: 96px; height: 96px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border-radius: 24px; margin: 0 auto 24px; box-shadow: 0 16px 32px -8px rgba(99, 102, 241, 0.4);">
        <table role="presentation" width="100%" height="100%">
            <tr>
                <td align="center" valign="middle">
<img src="{{ asset('images/email/user.webp') }}" alt="Welcome" style="width: 48px; height: 48px;">
                </td>
            </tr>
        </table>
    </div>
    <h1 style="font-size: 28px; font-weight: 700; color: #0f172a; margin: 0 0 12px; line-height: 1.3;">Welcome, {{ $user->name }}!</h1>
    <p style="font-size: 16px; color: #64748b; margin: 0;">Your account has been created successfully</p>
</div>
@endsection

@section('content')
<div style="padding: 0 40px 40px;">
    <!-- Intro Text -->
    <p style="font-size: 16px; color: #334155; margin: 0 0 24px; text-align: center;">
        We're excited to have you on board! Here's what you can do with your new account:
    </p>

    <!-- Feature 1: Buy Products -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 1px solid #93c5fd; border-radius: 12px; margin-bottom: 12px;">
        <tr>
            <td style="padding: 20px; width: 60px; vertical-align: top;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 12px;">
                    <table role="presentation" width="100%" height="100%">
                        <tr>
                            <td align="center" valign="middle">
<img src="{{ asset('images/email/package.webp') }}" alt="Products" style="width: 24px; height: 24px;">
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td style="padding: 20px 20px 20px 0;">
                <p style="font-weight: 600; color: #1e40af; margin: 0 0 4px; font-size: 16px;">Buy Digital Products</p>
                <p style="color: #3b82f6; margin: 0; font-size: 14px;">Templates, UI kits, icons, graphics, and more</p>
            </td>
        </tr>
    </table>

    <!-- Feature 2: Hire Freelancers -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #86efac; border-radius: 12px; margin-bottom: 12px;">
        <tr>
            <td style="padding: 20px; width: 60px; vertical-align: top;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px;">
                    <table role="presentation" width="100%" height="100%">
                        <tr>
                            <td align="center" valign="middle">
<img src="{{ asset('images/email/user-plus.webp') }}" alt="Freelancers" style="width: 24px; height: 24px;">
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td style="padding: 20px 20px 20px 0;">
                <p style="font-weight: 600; color: #166534; margin: 0 0 4px; font-size: 16px;">Hire Freelancers</p>
                <p style="color: #22c55e; margin: 0; font-size: 14px;">Find experts for your projects</p>
            </td>
        </tr>
    </table>

    <!-- Feature 3: Sell Your Work -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); border: 1px solid #a5b4fc; border-radius: 12px; margin-bottom: 12px;">
        <tr>
            <td style="padding: 20px; width: 60px; vertical-align: top;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border-radius: 12px;">
                    <table role="presentation" width="100%" height="100%">
                        <tr>
                            <td align="center" valign="middle">
<img src="{{ asset('images/email/dollar-sign.webp') }}" alt="Sell" style="width: 24px; height: 24px;">
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td style="padding: 20px 20px 20px 0;">
                <p style="font-weight: 600; color: #4338ca; margin: 0 0 4px; font-size: 16px;">Sell Your Work</p>
                <p style="color: #6366f1; margin: 0; font-size: 14px;">Become a seller and earn money</p>
            </td>
        </tr>
    </table>

    <!-- CTA Button -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td align="center" style="padding: 32px 0;">
                <a href="{{ url('/dashboard') }}" style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 10px; box-shadow: 0 4px 14px -3px rgba(99, 102, 241, 0.5);">
                    Go to Dashboard
                </a>
            </td>
        </tr>
    </table>

    <!-- Divider -->
    <div style="height: 1px; background: linear-gradient(90deg, transparent 0%, #e2e8f0 50%, transparent 100%); margin: 0 0 32px;"></div>

    <!-- Quick Links -->
    <p style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px; text-align: center;">Quick Links</p>

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td align="center">
                <a href="{{ url('/products') }}" style="display: inline-block; padding: 10px 20px; background: #f1f5f9; color: #334155; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 500; margin: 4px;">Products</a>
                <a href="{{ url('/services') }}" style="display: inline-block; padding: 10px 20px; background: #f1f5f9; color: #334155; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 500; margin: 4px;">Services</a>
                <a href="{{ url('/profile') }}" style="display: inline-block; padding: 10px 20px; background: #f1f5f9; color: #334155; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 500; margin: 4px;">Profile</a>
            </td>
        </tr>
    </table>

    <!-- Divider -->
    <div style="height: 1px; background: linear-gradient(90deg, transparent 0%, #e2e8f0 50%, transparent 100%); margin: 32px 0;"></div>

    <!-- Support -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="text-align: center;">
                <p style="color: #64748b; font-size: 14px; margin: 0 0 8px;">Questions? We're here to help!</p>
                <a href="{{ url('/support') }}" style="color: #6366f1; font-size: 14px; text-decoration: none; font-weight: 500;">Contact Support</a>
            </td>
        </tr>
    </table>
</div>
@endsection
