<?php

namespace App\Services;

use App\Models\SecurityAlert;
use App\Models\User;
use App\Notifications\Security\AccountLockedNotification;
use App\Notifications\Security\AdminSecurityAlertNotification;
use App\Notifications\Security\NewDeviceLoginNotification;
use App\Notifications\Security\PasswordChangedNotification;
use App\Notifications\Security\SuspiciousLoginActivity;
use App\Notifications\Security\TwoFactorDisabledNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SecurityNotificationService
{
    /**
     * Threshold for suspicious login activity notifications.
     */
    protected const SUSPICIOUS_ATTEMPTS_THRESHOLD = 3;

    /**
     * Get the security notification email address.
     */
    protected function getSecurityEmail(): ?string
    {
        return config('app.security_email');
    }

    /**
     * Notify admins of suspicious login activity.
     */
    public function notifySuspiciousLogin(
        ?User $user,
        string $email,
        string $ipAddress,
        int $failedAttempts,
        ?string $userAgent = null
    ): void {
        if ($failedAttempts < self::SUSPICIOUS_ATTEMPTS_THRESHOLD) {
            return;
        }

        $securityEmail = $this->getSecurityEmail();
        if (!$securityEmail) {
            Log::warning('Security email not configured, cannot send suspicious login notification');
            return;
        }

        try {
            Notification::route('mail', $securityEmail)
                ->notify(new SuspiciousLoginActivity(
                    user: $user,
                    email: $email,
                    ipAddress: $ipAddress,
                    failedAttempts: $failedAttempts,
                    userAgent: $userAgent
                ));

            // Also create a security alert record
            SecurityAlert::createAlert(
                type: 'suspicious_login',
                title: "Suspicious login activity for {$email}",
                description: "Detected {$failedAttempts} failed login attempts from IP {$ipAddress}",
                severity: $failedAttempts >= 5 ? 'high' : 'medium',
                metadata: [
                    'email' => $email,
                    'user_id' => $user?->id,
                    'ip_address' => $ipAddress,
                    'failed_attempts' => $failedAttempts,
                    'user_agent' => $userAgent,
                ]
            );

            Log::channel('fraud')->info('Suspicious login notification sent', [
                'email' => $email,
                'ip' => $ipAddress,
                'attempts' => $failedAttempts,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to send suspicious login notification', [
                'error' => $e->getMessage(),
                'email' => $email,
            ]);
        }
    }

    /**
     * Notify when an account is locked.
     */
    public function notifyAccountLocked(
        User $user,
        string $ipAddress,
        int $failedAttempts,
        \DateTimeInterface $lockedUntil,
        ?string $userAgent = null
    ): void {
        $securityEmail = $this->getSecurityEmail();

        try {
            // Notify the user
            $user->notify(new AccountLockedNotification(
                user: $user,
                ipAddress: $ipAddress,
                failedAttempts: $failedAttempts,
                lockedUntil: $lockedUntil,
                userAgent: $userAgent
            ));

            // Notify security team
            if ($securityEmail) {
                Notification::route('mail', $securityEmail)
                    ->notify(new AccountLockedNotification(
                        user: $user,
                        ipAddress: $ipAddress,
                        failedAttempts: $failedAttempts,
                        lockedUntil: $lockedUntil,
                        userAgent: $userAgent
                    ));
            }

            // Create security alert
            SecurityAlert::createAlert(
                type: 'multiple_failed_logins',
                title: "Account locked: {$user->email}",
                description: "Account locked after {$failedAttempts} failed login attempts. IP: {$ipAddress}",
                severity: 'high',
                metadata: [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'ip_address' => $ipAddress,
                    'failed_attempts' => $failedAttempts,
                    'locked_until' => $lockedUntil->format('Y-m-d H:i:s'),
                ]
            );

            Log::channel('fraud')->warning('Account locked notification sent', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $ipAddress,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to send account locked notification', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
        }
    }

    /**
     * Notify user of login from new device/location.
     */
    public function notifyNewDeviceLogin(
        User $user,
        string $ipAddress,
        ?string $userAgent = null,
        ?string $location = null
    ): void {
        try {
            $user->notify(new NewDeviceLoginNotification(
                user: $user,
                ipAddress: $ipAddress,
                userAgent: $userAgent,
                location: $location
            ));

            Log::info('New device login notification sent', [
                'user_id' => $user->id,
                'ip' => $ipAddress,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to send new device login notification', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
        }
    }

    /**
     * Notify user when password is changed.
     */
    public function notifyPasswordChanged(
        User $user,
        string $ipAddress,
        ?string $userAgent = null
    ): void {
        try {
            $user->notify(new PasswordChangedNotification(
                user: $user,
                ipAddress: $ipAddress,
                userAgent: $userAgent
            ));

            Log::info('Password changed notification sent', [
                'user_id' => $user->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to send password changed notification', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
        }
    }

    /**
     * Notify user when 2FA is disabled.
     */
    public function notifyTwoFactorDisabled(
        User $user,
        string $ipAddress,
        ?string $userAgent = null
    ): void {
        $securityEmail = $this->getSecurityEmail();

        try {
            // Notify the user
            $user->notify(new TwoFactorDisabledNotification(
                user: $user,
                ipAddress: $ipAddress,
                userAgent: $userAgent
            ));

            // Also notify security team for admin accounts
            if ($securityEmail && $user->hasRole('admin')) {
                Notification::route('mail', $securityEmail)
                    ->notify(new AdminSecurityAlertNotification(
                        alertType: 'admin_2fa_disabled',
                        title: "Admin 2FA Disabled: {$user->email}",
                        description: "Two-factor authentication was disabled for admin account {$user->email}",
                        severity: AdminSecurityAlertNotification::SEVERITY_HIGH,
                        metadata: [
                            'user_id' => $user->id,
                            'email' => $user->email,
                            'ip_address' => $ipAddress,
                        ]
                    ));
            }

            Log::info('2FA disabled notification sent', [
                'user_id' => $user->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to send 2FA disabled notification', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
        }
    }

    /**
     * Send a generic security alert to admins.
     */
    public function sendAdminSecurityAlert(
        string $alertType,
        string $title,
        string $description,
        string $severity = AdminSecurityAlertNotification::SEVERITY_MEDIUM,
        array $metadata = []
    ): void {
        $securityEmail = $this->getSecurityEmail();
        if (!$securityEmail) {
            Log::warning('Security email not configured, cannot send admin security alert');
            return;
        }

        try {
            Notification::route('mail', $securityEmail)
                ->notify(new AdminSecurityAlertNotification(
                    alertType: $alertType,
                    title: $title,
                    description: $description,
                    severity: $severity,
                    metadata: $metadata
                ));

            // Also create a security alert record
            SecurityAlert::createAlert(
                type: $alertType,
                title: $title,
                description: $description,
                severity: $severity,
                metadata: $metadata
            );

            Log::channel('fraud')->info('Admin security alert sent', [
                'type' => $alertType,
                'title' => $title,
                'severity' => $severity,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to send admin security alert', [
                'error' => $e->getMessage(),
                'type' => $alertType,
            ]);
        }
    }

    /**
     * Notify about brute force attack detection.
     */
    public function notifyBruteForceDetected(
        string $ipAddress,
        int $attemptCount,
        array $targetedAccounts = []
    ): void {
        $this->sendAdminSecurityAlert(
            alertType: 'brute_force',
            title: "Brute Force Attack Detected from {$ipAddress}",
            description: "Detected {$attemptCount} login attempts from IP {$ipAddress} targeting multiple accounts.",
            severity: AdminSecurityAlertNotification::SEVERITY_CRITICAL,
            metadata: [
                'ip_address' => $ipAddress,
                'attempt_count' => $attemptCount,
                'targeted_accounts' => array_slice($targetedAccounts, 0, 10), // Limit to 10
                'detected_at' => now()->toIso8601String(),
            ]
        );
    }

    /**
     * Notify about suspicious IP activity.
     */
    public function notifySuspiciousIpActivity(
        string $ipAddress,
        string $reason,
        array $metadata = []
    ): void {
        $this->sendAdminSecurityAlert(
            alertType: 'suspicious_ip',
            title: "Suspicious Activity from IP: {$ipAddress}",
            description: $reason,
            severity: AdminSecurityAlertNotification::SEVERITY_HIGH,
            metadata: array_merge(['ip_address' => $ipAddress], $metadata)
        );
    }
}
