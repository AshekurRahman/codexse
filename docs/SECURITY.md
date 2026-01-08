# Security Documentation

## Overview

This document outlines the security measures implemented in the Codexse marketplace platform.

---

## Authentication Security

### Password Security
- **Hashing**: Bcrypt with cost factor of 12
- **Minimum Length**: 8 characters (recommended: 12+)
- **Complexity**: Mixed case, numbers, symbols recommended
- **History**: Last 5 passwords cannot be reused

### Rate Limiting
| Endpoint | Limit | Window |
|----------|-------|--------|
| Login | 5 attempts | 1 minute |
| Registration | 3 attempts | 1 minute |
| Password Reset | 3 attempts | 1 minute |
| Checkout | 5 attempts | 1 minute |
| API (License) | 30 requests | 1 minute |

### Account Lockout
- Lockout after 5 failed login attempts
- 30-minute lockout duration
- Admin can manually unlock accounts

### Two-Factor Authentication
- TOTP-based (Google Authenticator compatible)
- Recovery codes provided
- Required for admin accounts (recommended)

### Session Management
- Encrypted session storage
- 30-minute session lifetime
- Secure cookie flags:
  - `HttpOnly`: true
  - `Secure`: true (production)
  - `SameSite`: strict

---

## Authorization

### Role-Based Access Control
Using Spatie Laravel Permission:

| Role | Permissions |
|------|-------------|
| User | Browse, purchase, download |
| Seller | + Product management, sales view |
| Admin | Full access to admin panel |
| Super Admin | + User management, settings |

### Policy Enforcement
All models have corresponding policies:
- `ProductPolicy`
- `ServicePolicy`
- `OrderPolicy`
- `DisputePolicy`
- `SubscriptionPolicy`
- `JobPostingPolicy`
- `JobContractPolicy`
- `ServiceOrderPolicy`

---

## Input Validation & Sanitization

### Request Validation
All form inputs are validated using Laravel Form Requests:

```php
$request->validate([
    'email' => 'required|email:rfc,dns',
    'name' => 'required|string|max:255',
    'amount' => 'required|numeric|min:0|max:10000',
]);
```

### XSS Prevention
- Input sanitization middleware on all requests
- HTML Purifier for rich text content
- Blade escaping by default: `{{ $variable }}`

### SQL Injection Prevention
- Eloquent ORM with parameterized queries
- No raw SQL queries with user input
- Query builder bindings for complex queries

---

## Payment Security

### Stripe
- Client-side tokenization (no card data on server)
- Webhook signature verification
- Idempotency keys for duplicate prevention

### PayPal
- OAuth 2.0 authentication
- Server-side order creation
- Webhook signature verification

### Wallet System
- Pessimistic locking prevents race conditions
- Double-entry ledger for audit trail
- Hold/release pattern for pending transactions
- Idempotency keys prevent duplicates

---

## Data Protection

### Mass Assignment Protection
Sensitive fields are NOT in `$fillable`:

```php
// User model - protected fields
// 'is_admin' - NEVER mass assignable

// Wallet model - protected fields
// 'balance', 'pending_balance', 'held_balance' - NEVER mass assignable
```

### Encrypted Fields
Using Laravel's `encrypted` cast:
```php
protected $casts = [
    'two_factor_secret' => 'encrypted',
    'two_factor_recovery_codes' => 'encrypted:array',
];
```

### Backup Encryption
- AES-256 encryption for backup archives
- Set `BACKUP_ARCHIVE_PASSWORD` in .env

---

## API Security

### License API Authentication
```http
X-API-Key: your-64-character-hex-key
```

- API key stored in database (Settings table)
- Timing-safe comparison prevents timing attacks
- Requests without key are rejected (503)

### Webhook Verification
All payment webhooks verify signatures:

```php
// Stripe
$event = Webhook::constructEvent($payload, $sig, $secret);

// Payoneer
$expected = hash_hmac('sha256', $payload, $secret);
hash_equals($expected, $signature);
```

---

## File Upload Security

### Validation
```php
'file' => [
    'required',
    'file',
    'max:102400',  // 100MB
    'mimes:zip,rar,pdf,doc,docx',
],
```

### Storage
- Files stored outside web root
- Randomized filenames
- Served through authenticated controller

### Path Traversal Prevention
```php
$safeFilename = basename($filename);
if ($safeFilename !== $filename) {
    abort(403);
}
```

---

## Security Headers

### Middleware: `SecurityHeaders`

```php
// Strict Transport Security
'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains'

// Content Security Policy
'Content-Security-Policy' => "default-src 'self'; script-src 'self' https://js.stripe.com ..."

// Other Headers
'X-Content-Type-Options' => 'nosniff'
'X-Frame-Options' => 'SAMEORIGIN'
'X-XSS-Protection' => '1; mode=block'
'Referrer-Policy' => 'strict-origin-when-cross-origin'
```

---

## Logging & Monitoring

### Activity Logging
All security-relevant actions are logged:
- Login success/failure
- Password changes
- 2FA enable/disable
- Admin actions
- Payment transactions
- Download access

### Log Location
```
storage/logs/laravel.log
storage/logs/errors.log
```

### Sensitive Data Masking
Logs do not contain:
- Passwords
- API keys
- Credit card numbers
- Session tokens

---

## Security Checklist

### Production Deployment
- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] `SESSION_ENCRYPT=true`
- [ ] `SESSION_SECURE_COOKIE=true`
- [ ] HTTPS enforced
- [ ] Database credentials rotated
- [ ] Backup password configured
- [ ] 2FA enabled for admins
- [ ] Rate limiting active
- [ ] Security headers configured

### Regular Maintenance
- [ ] Review access logs weekly
- [ ] Update dependencies monthly
- [ ] Rotate API keys quarterly
- [ ] Security audit annually
- [ ] Backup verification weekly

---

## Reporting Vulnerabilities

If you discover a security vulnerability, please email security@codexse.com.

Do NOT create public issues for security vulnerabilities.
