<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'alert_type',
        'severity',
        'title',
        'description',
        'metadata',
        'is_resolved',
        'resolved_by',
        'resolved_at',
        'resolution_notes',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'is_resolved' => 'boolean',
            'resolved_at' => 'datetime',
        ];
    }

    public function resolvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public const ALERT_TYPES = [
        'brute_force' => 'Brute Force Attack',
        'sql_injection' => 'SQL Injection Attempt',
        'xss_attack' => 'XSS Attack',
        'suspicious_login' => 'Suspicious Login',
        'multiple_failed_logins' => 'Multiple Failed Logins',
        'unusual_activity' => 'Unusual Activity',
        'data_breach' => 'Potential Data Breach',
        'malware_detected' => 'Malware Detected',
        'ddos_attack' => 'DDoS Attack',
        'api_abuse' => 'API Abuse',
    ];

    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }

    public function scopeResolved($query)
    {
        return $query->where('is_resolved', true);
    }

    public function scopeBySeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    public function resolve(int $userId, ?string $notes = null): void
    {
        $this->update([
            'is_resolved' => true,
            'resolved_by' => $userId,
            'resolved_at' => now(),
            'resolution_notes' => $notes,
        ]);
    }

    public static function createAlert(
        string $type,
        string $title,
        string $description,
        string $severity = 'medium',
        array $metadata = []
    ): self {
        return self::create([
            'alert_type' => $type,
            'severity' => $severity,
            'title' => $title,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }

    public function getSeverityColorAttribute(): string
    {
        return match ($this->severity) {
            'critical' => 'danger',
            'high' => 'warning',
            'medium' => 'info',
            'low' => 'gray',
            default => 'gray',
        };
    }
}
