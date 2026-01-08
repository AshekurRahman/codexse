# API Documentation

## Overview

The Codexse API provides programmatic access to license validation and activation for software products.

**Base URL:** `https://yourdomain.com/api`

---

## Authentication

All API requests require authentication via API key.

### Header Authentication (Recommended)

```http
X-API-Key: your-api-key-here
```

### Query Parameter Authentication

```http
GET /api/license/XXXX-XXXX-XXXX-XXXX?api_key=your-api-key-here
```

### Getting an API Key

API keys are managed by administrators in the Settings panel. Contact your administrator to obtain an API key.

---

## Rate Limiting

| Endpoint Group | Limit | Window |
|----------------|-------|--------|
| License API | 30 requests | 1 minute |
| Chatbot API | 20 requests | 1 minute |
| Product API | 60 requests | 1 minute |

When rate limited, you'll receive:

```http
HTTP/1.1 429 Too Many Requests
Retry-After: 60
```

---

## License API

### Validate License

Check if a license key is valid.

```http
POST /api/license/validate
Content-Type: application/json
X-API-Key: your-api-key
```

**Request Body:**
```json
{
  "license_key": "XXXX-XXXX-XXXX-XXXX",
  "product_id": 1
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| license_key | string | Yes | 19-character license key |
| product_id | integer | No | Filter by specific product |

**Success Response (200):**
```json
{
  "valid": true,
  "license": {
    "license_key": "ABCD-EFGH-IJKL-MNOP",
    "license_type": "regular",
    "status": "active",
    "product": {
      "id": 1,
      "name": "My Product"
    },
    "activations": {
      "used": 1,
      "max": 1,
      "remaining": 0
    },
    "expires_at": "2027-01-01T00:00:00+00:00",
    "activated_at": "2026-01-01T00:00:00+00:00"
  }
}
```

**Error Response (400):**
```json
{
  "valid": false,
  "error": "License key not found"
}
```

**Possible Errors:**
- `License key not found`
- `License has been revoked`
- `License is suspended`
- `License has expired`

---

### Activate License

Activate a license for a domain or machine.

```http
POST /api/license/activate
Content-Type: application/json
X-API-Key: your-api-key
```

**Request Body:**
```json
{
  "license_key": "XXXX-XXXX-XXXX-XXXX",
  "domain": "example.com",
  "machine_id": "unique-machine-identifier"
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| license_key | string | Yes | 19-character license key |
| domain | string | No | Domain to activate for |
| machine_id | string | No | Unique machine identifier |

**Success Response (200):**
```json
{
  "success": true,
  "activation_id": 123,
  "activations_remaining": 0
}
```

**Already Activated Response (200):**
```json
{
  "success": true,
  "message": "Already activated",
  "activation_id": 123,
  "activations_remaining": 0
}
```

**Error Response (400):**
```json
{
  "success": false,
  "error": "Maximum activations reached",
  "activations_used": 1,
  "activations_max": 1
}
```

---

### Deactivate License

Remove an activation from a license.

```http
POST /api/license/deactivate
Content-Type: application/json
X-API-Key: your-api-key
```

**Request Body:**
```json
{
  "license_key": "XXXX-XXXX-XXXX-XXXX",
  "activation_id": 123
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| license_key | string | Yes | 19-character license key |
| activation_id | integer | Yes | ID of activation to remove |

**Success Response (200):**
```json
{
  "success": true,
  "message": "License deactivated successfully"
}
```

---

### Get License Details

Retrieve full license information.

```http
GET /api/license/{license_key}
X-API-Key: your-api-key
```

**Success Response (200):**
```json
{
  "success": true,
  "license": {
    "license_key": "ABCD-EFGH-IJKL-MNOP",
    "license_type": "extended",
    "status": "active",
    "product": {
      "id": 1,
      "name": "My Product"
    },
    "activations": {
      "used": 2,
      "max": 5,
      "remaining": 3,
      "list": [
        {
          "id": 1,
          "domain": "site1.com",
          "ip_address": "192.168.1.1",
          "is_active": true,
          "created_at": "2026-01-01T00:00:00+00:00"
        },
        {
          "id": 2,
          "domain": "site2.com",
          "ip_address": "192.168.1.2",
          "is_active": true,
          "created_at": "2026-01-02T00:00:00+00:00"
        }
      ]
    },
    "expires_at": null,
    "activated_at": "2026-01-01T00:00:00+00:00",
    "created_at": "2026-01-01T00:00:00+00:00"
  }
}
```

---

## Error Responses

### Authentication Errors

**Missing API Key (401):**
```json
{
  "success": false,
  "error": "API key required"
}
```

**Invalid API Key (401):**
```json
{
  "success": false,
  "error": "Invalid API key"
}
```

**API Not Configured (503):**
```json
{
  "success": false,
  "error": "API not configured. Please contact administrator."
}
```

### Validation Errors

**Invalid Request (422):**
```json
{
  "message": "The license key field is required.",
  "errors": {
    "license_key": [
      "The license key field is required."
    ]
  }
}
```

---

## Code Examples

### PHP

```php
<?php
$apiKey = 'your-api-key';
$licenseKey = 'XXXX-XXXX-XXXX-XXXX';

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'https://yourdomain.com/api/license/validate',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'X-API-Key: ' . $apiKey,
    ],
    CURLOPT_POSTFIELDS => json_encode([
        'license_key' => $licenseKey,
    ]),
]);

$response = curl_exec($ch);
$result = json_decode($response, true);

if ($result['valid']) {
    echo "License is valid!";
} else {
    echo "License error: " . $result['error'];
}
```

### JavaScript

```javascript
async function validateLicense(licenseKey) {
    const response = await fetch('https://yourdomain.com/api/license/validate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-API-Key': 'your-api-key',
        },
        body: JSON.stringify({
            license_key: licenseKey,
        }),
    });

    const result = await response.json();

    if (result.valid) {
        console.log('License is valid!');
        return true;
    } else {
        console.error('License error:', result.error);
        return false;
    }
}
```

### Python

```python
import requests

API_KEY = 'your-api-key'
BASE_URL = 'https://yourdomain.com/api'

def validate_license(license_key):
    response = requests.post(
        f'{BASE_URL}/license/validate',
        headers={
            'Content-Type': 'application/json',
            'X-API-Key': API_KEY,
        },
        json={
            'license_key': license_key,
        }
    )

    result = response.json()

    if result.get('valid'):
        print('License is valid!')
        return True
    else:
        print(f"License error: {result.get('error')}")
        return False
```

### C#

```csharp
using System.Net.Http;
using System.Text.Json;

public async Task<bool> ValidateLicense(string licenseKey)
{
    using var client = new HttpClient();
    client.DefaultRequestHeaders.Add("X-API-Key", "your-api-key");

    var content = new StringContent(
        JsonSerializer.Serialize(new { license_key = licenseKey }),
        System.Text.Encoding.UTF8,
        "application/json"
    );

    var response = await client.PostAsync(
        "https://yourdomain.com/api/license/validate",
        content
    );

    var json = await response.Content.ReadAsStringAsync();
    var result = JsonSerializer.Deserialize<LicenseResponse>(json);

    return result.Valid;
}
```

---

## Webhooks

### License Events (Coming Soon)

Subscribe to license events:
- `license.activated`
- `license.deactivated`
- `license.expired`
- `license.revoked`

---

## SDK Libraries

Official SDKs (Coming Soon):
- PHP SDK
- JavaScript/Node.js SDK
- Python SDK
- .NET SDK
