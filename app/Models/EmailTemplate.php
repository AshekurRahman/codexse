<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'thumbnail',
        'description',
        'subject',
        'html_content',
        'category',
        'variables',
        'is_active',
        'is_system',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_system' => 'boolean',
            'variables' => 'array',
        ];
    }

    public const CATEGORIES = [
        'general' => 'General',
        'order' => 'Orders',
        'seller' => 'Seller',
        'user' => 'User Account',
        'marketing' => 'Marketing',
        'service' => 'Services',
    ];

    /**
     * Default transactional email templates
     */
    public const DEFAULT_TEMPLATES = [
        'order_confirmation' => [
            'name' => 'Order Confirmation',
            'subject' => 'Order Confirmed - #{{order_number}}',
            'category' => 'order',
            'variables' => ['order_number', 'customer_name', 'order_total', 'order_items', 'order_date'],
        ],
        'order_shipped' => [
            'name' => 'Order Shipped',
            'subject' => 'Your order #{{order_number}} has shipped!',
            'category' => 'order',
            'variables' => ['order_number', 'customer_name', 'tracking_number', 'carrier', 'tracking_url'],
        ],
        'order_delivered' => [
            'name' => 'Order Delivered',
            'subject' => 'Your order #{{order_number}} has been delivered!',
            'category' => 'order',
            'variables' => ['order_number', 'customer_name', 'delivery_date'],
        ],
        'welcome_email' => [
            'name' => 'Welcome Email',
            'subject' => 'Welcome to {{site_name}}!',
            'category' => 'user',
            'variables' => ['user_name', 'site_name', 'login_url'],
        ],
        'password_reset' => [
            'name' => 'Password Reset',
            'subject' => 'Reset Your Password',
            'category' => 'user',
            'variables' => ['user_name', 'reset_link', 'expiry_minutes'],
        ],
        'seller_application_approved' => [
            'name' => 'Seller Application Approved',
            'subject' => 'Congratulations! Your Seller Application is Approved',
            'category' => 'seller',
            'variables' => ['seller_name', 'store_name', 'dashboard_url'],
        ],
        'service_order_new' => [
            'name' => 'New Service Order',
            'subject' => 'New Service Order - {{service_name}}',
            'category' => 'service',
            'variables' => ['seller_name', 'buyer_name', 'service_name', 'package_name', 'order_total'],
        ],
        'video_call_scheduled' => [
            'name' => 'Video Call Scheduled',
            'subject' => 'Video Call Scheduled for {{scheduled_date}}',
            'category' => 'service',
            'variables' => ['user_name', 'other_party', 'scheduled_date', 'scheduled_time', 'join_url'],
        ],
    ];

    public function campaigns(): HasMany
    {
        return $this->hasMany(EmailCampaign::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeTransactional($query)
    {
        return $query->where('is_system', true);
    }

    public function renderContent(array $variables = []): string
    {
        $content = $this->html_content;

        foreach ($variables as $key => $value) {
            if (is_string($value) || is_numeric($value)) {
                $content = str_replace('{{' . $key . '}}', $value, $content);
                $content = str_replace('{{ ' . $key . ' }}', $value, $content);
            }
        }

        return $content;
    }

    /**
     * Render template with given variables (returns subject and body)
     */
    public function render(array $data): array
    {
        $subject = $this->subject ?? $this->name;
        $body = $this->html_content;

        foreach ($data as $key => $value) {
            if (is_string($value) || is_numeric($value)) {
                $subject = str_replace('{{' . $key . '}}', $value, $subject);
                $subject = str_replace('{{ ' . $key . ' }}', $value, $subject);
                $body = str_replace('{{' . $key . '}}', $value, $body);
                $body = str_replace('{{ ' . $key . ' }}', $value, $body);
            }
        }

        return [
            'subject' => $subject,
            'body' => $body,
        ];
    }

    /**
     * Get template by slug
     */
    public static function getBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->where('is_active', true)->first();
    }
}
