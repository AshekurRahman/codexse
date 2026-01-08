# Codexse Marketplace Documentation

A comprehensive digital marketplace built with Laravel 11, Livewire 3, and Filament 3.2.

## Table of Contents

1. [Installation](#installation)
2. [Configuration](#configuration)
3. [Features](#features)
4. [Security](#security)
5. [API Reference](#api-reference)
6. [Admin Panel](#admin-panel)
7. [Scheduled Tasks](#scheduled-tasks)
8. [Troubleshooting](#troubleshooting)

---

## Installation

### Requirements

- PHP 8.2+
- MySQL 8.0+
- Composer 2.x
- Node.js 18+ (for asset compilation)
- Redis (optional, for caching/queues)

### Quick Start

```bash
# Clone repository
git clone <repository-url>
cd codexse

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Build assets
npm run build

# Create storage link
php artisan storage:link

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## Configuration

### Environment Variables

#### Application
```env
APP_NAME=Codexse
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

#### Database
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=codexse
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

#### Session Security
```env
SESSION_DRIVER=database
SESSION_LIFETIME=30
SESSION_ENCRYPT=true
```

#### Payment Gateways

**Stripe:**
```env
STRIPE_KEY=pk_live_xxx
STRIPE_SECRET=sk_live_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx
```

**PayPal:**
```env
PAYPAL_MODE=live
PAYPAL_CLIENT_ID=xxx
PAYPAL_SECRET=xxx
```

**Payoneer:**
```env
PAYONEER_PROGRAM_ID=xxx
PAYONEER_API_USERNAME=xxx
PAYONEER_API_PASSWORD=xxx
PAYONEER_WEBHOOK_SECRET=xxx
```

#### Backup Encryption
```env
BACKUP_ARCHIVE_PASSWORD=your-secure-backup-password
BACKUP_NOTIFY_EMAIL=admin@yourdomain.com
```

---

## Features

### Authentication
- Email/password login with rate limiting
- Social login (Google, GitHub, Facebook)
- Two-factor authentication (TOTP)
- Email verification
- Password history (prevents reuse)
- Account lockout after failed attempts
- Session management (view/revoke sessions)
- Trusted devices

### Wallet System
- Ledger-based double-entry accounting
- Atomic balance updates with pessimistic locking
- Hold/release pattern (like Stripe authorize/capture)
- Idempotency keys prevent duplicate transactions
- Partial payments (wallet + card)
- Deposit via Stripe
- Withdrawal requests with admin approval

### Shopping
- Product catalog with categories
- Advanced search with filters
- Shopping cart (session-based)
- Wishlist (authenticated users)
- Product reviews and ratings
- Recently viewed products
- Product comparison
- Product bundles

### Checkout & Payments
- Multiple payment methods (Stripe, PayPal, Payoneer, Wallet)
- Partial wallet payments
- Coupon/discount codes with usage limits
- Tax calculation
- Order confirmation emails
- Invoice generation (PDF)

### Downloads & Licenses
- Secure file downloads (authenticated)
- Download limits per product
- License key generation (XXXX-XXXX-XXXX-XXXX format)
- License activation/deactivation
- Activation limits per license type
- License API for software integration

### Referral Program
- Unique referral codes per user
- Commission on referred purchases
- Automatic wallet credits
- Referral tracking and analytics

### Seller Features
- Seller application and approval
- Product management (CRUD)
- Earnings dashboard
- Payout requests
- Sales analytics
- Commission rates (configurable per seller)

### GDPR Compliance
- Data export (JSON/ZIP)
- Account deletion with anonymization
- Consent logging
- Cookie consent
- Newsletter double opt-in

---

## Security

### Implemented Security Measures

#### Authentication Security
- Bcrypt password hashing
- Rate limiting on login (5 attempts/minute)
- Account lockout after failed attempts
- Two-factor authentication
- Session encryption
- Secure cookie flags (HttpOnly, Secure, SameSite=Strict)

#### Input Validation
- Request validation on all endpoints
- XSS protection via input sanitization
- SQL injection prevention (Eloquent ORM)
- CSRF protection on all forms

#### Authorization
- Policy-based authorization
- Role-based access control (Spatie Permissions)
- Route model binding with ownership checks

#### Data Protection
- Mass assignment protection (guarded fields)
- Encrypted session storage
- Encrypted backup archives
- No sensitive data in logs

#### API Security
- API key authentication for license endpoints
- Rate limiting (30 requests/minute)
- Request signature verification for webhooks

#### Infrastructure
- HTTPS enforcement
- Security headers (CSP, HSTS, X-Frame-Options, etc.)
- IP blocking for malicious actors
- Honeypot protection for bots

### Security Headers

The following headers are set via `SecurityHeaders` middleware:

```
Strict-Transport-Security: max-age=31536000; includeSubDomains
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
X-XSS-Protection: 1; mode=block
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: geolocation=(), microphone=(), camera=()
Content-Security-Policy: [configured per environment]
```

---

## API Reference

### License API

Base URL: `https://yourdomain.com/api/license`

#### Authentication

All requests require the `X-API-Key` header:

```
X-API-Key: your-api-key-here
```

#### Endpoints

##### Validate License

```http
POST /api/license/validate
Content-Type: application/json
X-API-Key: your-api-key

{
  "license_key": "XXXX-XXXX-XXXX-XXXX",
  "product_id": 1  // optional
}
```

**Response (Valid):**
```json
{
  "valid": true,
  "license": {
    "license_key": "XXXX-XXXX-XXXX-XXXX",
    "license_type": "regular",
    "status": "active",
    "product": {
      "id": 1,
      "name": "Product Name"
    },
    "activations": {
      "used": 1,
      "max": 1,
      "remaining": 0
    },
    "expires_at": null,
    "activated_at": "2026-01-01T00:00:00+00:00"
  }
}
```

**Response (Invalid):**
```json
{
  "valid": false,
  "error": "License key not found"
}
```

##### Activate License

```http
POST /api/license/activate
Content-Type: application/json
X-API-Key: your-api-key

{
  "license_key": "XXXX-XXXX-XXXX-XXXX",
  "domain": "example.com",
  "machine_id": "optional-machine-identifier"
}
```

**Response:**
```json
{
  "success": true,
  "activation_id": 123,
  "activations_remaining": 0
}
```

##### Deactivate License

```http
POST /api/license/deactivate
Content-Type: application/json
X-API-Key: your-api-key

{
  "license_key": "XXXX-XXXX-XXXX-XXXX",
  "activation_id": 123
}
```

##### Get License Details

```http
GET /api/license/{license_key}
X-API-Key: your-api-key
```

#### Error Responses

| Status | Error |
|--------|-------|
| 401 | API key required / Invalid API key |
| 404 | License not found |
| 400 | Validation error / Maximum activations reached |
| 429 | Rate limit exceeded |
| 503 | API not configured |

---

## Admin Panel

Access: `https://yourdomain.com/admin`

### Resources

| Resource | Description |
|----------|-------------|
| Users | Manage user accounts |
| Sellers | Approve/manage sellers |
| Products | Product catalog management |
| Services | Service listings |
| Orders | View/manage orders |
| Refunds | Process refunds |
| Wallets | View wallet balances |
| Payouts | Approve payout requests |
| Coupons | Discount code management |
| Categories | Product categories |
| Reviews | Moderate reviews |
| Disputes | Handle disputes |
| Support Tickets | Customer support |
| Newsletter | Subscriber management |

### Settings Pages

| Page | Description |
|------|-------------|
| General Settings | Site name, logo, etc. |
| Payment Settings | Gateway configuration |
| Email Settings | SMTP configuration |
| Commission Settings | Platform fees |
| Backup Manager | Database backups |
| System Monitoring | Server health |
| Security Dashboard | Security overview |
| Activity Log | User activity tracking |

---

## Scheduled Tasks

Add to crontab:
```bash
* * * * * cd /path/to/codexse && php artisan schedule:run >> /dev/null 2>&1
```

### Configured Tasks

| Schedule | Command | Description |
|----------|---------|-------------|
| Every minute | `queue:work` | Process queued jobs |
| Every minute | `wallet:expire-holds` | Expire wallet holds |
| Hourly | `campaigns:process` | Process email campaigns |
| Daily 00:00 | `escrow:auto-release` | Auto-release escrow |
| Daily 01:00 | `wallet:cleanup-idempotency` | Clean idempotency keys |
| Daily 02:00 | `backup:run --only-db` | Database backup |
| Daily 03:00 | `backup:clean` | Cleanup old backups |
| Daily 04:00 | `backup:monitor` | Monitor backup health |

---

## Troubleshooting

### Common Issues

#### 500 Server Error
```bash
# Check Laravel log
tail -100 storage/logs/laravel.log

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

#### Payment Webhook Not Working
1. Verify webhook URL is accessible
2. Check webhook secret is configured
3. Review `storage/logs/laravel.log` for errors
4. Ensure CSRF exception is configured for webhook routes

#### Email Not Sending
```bash
# Test mail configuration
php artisan tinker
>>> Mail::raw('Test', fn($m) => $m->to('test@example.com'));
```

#### Queue Jobs Not Processing
```bash
# Check queue status
php artisan queue:work --once

# Restart queue worker
php artisan queue:restart
```

#### Backup Failing
```bash
# Test backup manually
php artisan backup:run --only-db

# Check disk space
df -h

# Verify backup disk configuration
php artisan tinker
>>> Storage::disk('backups')->exists('.');
```

### Useful Commands

```bash
# Clear all caches
php artisan optimize:clear

# Regenerate autoload
composer dump-autoload

# Run migrations
php artisan migrate

# Check routes
php artisan route:list

# Check scheduled tasks
php artisan schedule:list

# Create admin user
php artisan tinker
>>> User::create(['name'=>'Admin','email'=>'admin@example.com','password'=>bcrypt('password'),'is_admin'=>true]);
```

---

## Support

For issues and feature requests, please create an issue in the repository.
