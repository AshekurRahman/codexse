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
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21M16 7C16 9.20914 14.2091 11 12 11C9.79086 11 8 9.20914 8 7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7Z" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
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
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 7L12 3L4 7M20 7L12 11M20 7V17L12 21M12 11L4 7M12 11V21M4 7V17L12 21" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
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
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16 21V19C16 17.9391 15.5786 16.9217 14.8284 16.1716C14.0783 15.4214 13.0609 15 12 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21M20 8V14M23 11H17M12.5 7C12.5 9.20914 10.7091 11 8.5 11C6.29086 11 4.5 9.20914 4.5 7C4.5 4.79086 6.29086 3 8.5 3C10.7091 3 12.5 4.79086 12.5 7Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
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
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 1V23M17 5H9.5C8.57174 5 7.6815 5.36875 7.02513 6.02513C6.36875 6.6815 6 7.57174 6 8.5C6 9.42826 6.36875 10.3185 7.02513 10.9749C7.6815 11.6313 8.57174 12 9.5 12H14.5C15.4283 12 16.3185 12.3687 16.9749 13.0251C17.6313 13.6815 18 14.5717 18 15.5C18 16.4283 17.6313 17.3185 16.9749 17.9749C16.3185 18.6313 15.4283 19 14.5 19H6" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
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
