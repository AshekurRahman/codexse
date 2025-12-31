<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Minimal Clean',
                'slug' => 'minimal-clean',
                'description' => 'A clean, minimal design with plenty of white space. Perfect for announcements and updates.',
                'category' => 'general',
                'sort_order' => 1,
                'html_content' => $this->getMinimalCleanTemplate(),
            ],
            [
                'name' => 'Gradient Hero',
                'slug' => 'gradient-hero',
                'description' => 'Eye-catching gradient header with modern typography. Great for promotions and launches.',
                'category' => 'promotional',
                'sort_order' => 2,
                'html_content' => $this->getGradientHeroTemplate(),
            ],
            [
                'name' => 'Dark Mode',
                'slug' => 'dark-mode',
                'description' => 'Sleek dark theme design with vibrant accent colors. Perfect for tech and gaming.',
                'category' => 'general',
                'sort_order' => 3,
                'html_content' => $this->getDarkModeTemplate(),
            ],
            [
                'name' => 'Card Layout',
                'slug' => 'card-layout',
                'description' => 'Multiple card sections for showcasing products or features.',
                'category' => 'promotional',
                'sort_order' => 4,
                'html_content' => $this->getCardLayoutTemplate(),
            ],
            [
                'name' => 'Newsletter Classic',
                'slug' => 'newsletter-classic',
                'description' => 'Traditional newsletter layout with header image and structured content.',
                'category' => 'newsletter',
                'sort_order' => 5,
                'html_content' => $this->getNewsletterClassicTemplate(),
            ],
            [
                'name' => 'Welcome Series',
                'slug' => 'welcome-series',
                'description' => 'Warm and inviting design for welcoming new subscribers.',
                'category' => 'onboarding',
                'sort_order' => 6,
                'html_content' => $this->getWelcomeSeriesTemplate(),
            ],
            [
                'name' => 'Sale Blast',
                'slug' => 'sale-blast',
                'description' => 'Bold and attention-grabbing design for sales and discounts.',
                'category' => 'promotional',
                'sort_order' => 7,
                'html_content' => $this->getSaleBlastTemplate(),
            ],
            [
                'name' => 'Product Showcase',
                'slug' => 'product-showcase',
                'description' => 'Elegant layout for featuring products with images and descriptions.',
                'category' => 'promotional',
                'sort_order' => 8,
                'html_content' => $this->getProductShowcaseTemplate(),
            ],
            [
                'name' => 'Simple Text',
                'slug' => 'simple-text',
                'description' => 'Text-focused design that feels personal and direct.',
                'category' => 'general',
                'sort_order' => 9,
                'html_content' => $this->getSimpleTextTemplate(),
            ],
            [
                'name' => 'Modern Split',
                'slug' => 'modern-split',
                'description' => 'Contemporary split-screen design with image and text columns.',
                'category' => 'general',
                'sort_order' => 10,
                'html_content' => $this->getModernSplitTemplate(),
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
    }

    private function getMinimalCleanTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{subject}}</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc;">
    <div style="max-width: 600px; margin: 0 auto; padding: 40px 20px;">
        <div style="background: #ffffff; border-radius: 16px; padding: 48px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="text-align: center; margin-bottom: 32px;">
                <h1 style="color: #1e293b; font-size: 28px; margin: 0; font-weight: 700;">{{app_name}}</h1>
            </div>
            <div style="color: #475569; font-size: 16px; line-height: 1.7;">
                {{content}}
            </div>
            <div style="margin-top: 40px; padding-top: 32px; border-top: 1px solid #e2e8f0; text-align: center;">
                <p style="color: #94a3b8; font-size: 13px; margin: 0;">© {{year}} {{app_name}}. All rights reserved.</p>
                <p style="margin: 12px 0 0;"><a href="{{unsubscribe_url}}" style="color: #94a3b8; font-size: 13px;">Unsubscribe</a></p>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getGradientHeroTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{subject}}</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9;">
    <div style="max-width: 600px; margin: 0 auto;">
        <div style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%); padding: 60px 40px; text-align: center; border-radius: 0 0 24px 24px;">
            <h1 style="color: #ffffff; font-size: 32px; margin: 0 0 16px; font-weight: 800;">{{app_name}}</h1>
            <p style="color: rgba(255,255,255,0.9); font-size: 18px; margin: 0;">{{preview_text}}</p>
        </div>
        <div style="background: #ffffff; margin: -20px 20px 20px; border-radius: 16px; padding: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
            <div style="color: #334155; font-size: 16px; line-height: 1.7;">
                {{content}}
            </div>
            <div style="text-align: center; margin-top: 32px;">
                <a href="{{cta_url}}" style="display: inline-block; background: linear-gradient(135deg, #6366f1, #8b5cf6); color: #ffffff; padding: 14px 32px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 15px;">{{cta_text}}</a>
            </div>
        </div>
        <div style="text-align: center; padding: 20px;">
            <p style="color: #94a3b8; font-size: 13px; margin: 0;">© {{year}} {{app_name}}</p>
            <p style="margin: 8px 0 0;"><a href="{{unsubscribe_url}}" style="color: #94a3b8; font-size: 13px;">Unsubscribe</a></p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getDarkModeTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{subject}}</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #0f172a;">
    <div style="max-width: 600px; margin: 0 auto; padding: 40px 20px;">
        <div style="background: #1e293b; border-radius: 16px; padding: 48px; border: 1px solid #334155;">
            <div style="text-align: center; margin-bottom: 32px;">
                <h1 style="color: #f8fafc; font-size: 28px; margin: 0; font-weight: 700;">{{app_name}}</h1>
                <div style="width: 60px; height: 4px; background: linear-gradient(90deg, #06b6d4, #8b5cf6); margin: 16px auto 0; border-radius: 2px;"></div>
            </div>
            <div style="color: #cbd5e1; font-size: 16px; line-height: 1.7;">
                {{content}}
            </div>
            <div style="text-align: center; margin-top: 32px;">
                <a href="{{cta_url}}" style="display: inline-block; background: linear-gradient(90deg, #06b6d4, #8b5cf6); color: #ffffff; padding: 14px 32px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 15px;">{{cta_text}}</a>
            </div>
            <div style="margin-top: 40px; padding-top: 32px; border-top: 1px solid #334155; text-align: center;">
                <p style="color: #64748b; font-size: 13px; margin: 0;">© {{year}} {{app_name}}. All rights reserved.</p>
                <p style="margin: 12px 0 0;"><a href="{{unsubscribe_url}}" style="color: #64748b; font-size: 13px;">Unsubscribe</a></p>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getCardLayoutTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{subject}}</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc;">
    <div style="max-width: 600px; margin: 0 auto; padding: 40px 20px;">
        <div style="text-align: center; margin-bottom: 32px;">
            <h1 style="color: #1e293b; font-size: 28px; margin: 0; font-weight: 700;">{{app_name}}</h1>
        </div>
        <div style="background: #ffffff; border-radius: 16px; padding: 32px; margin-bottom: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="color: #475569; font-size: 16px; line-height: 1.7;">
                {{content}}
            </div>
        </div>
        <div style="display: flex; gap: 16px;">
            <div style="flex: 1; background: #ffffff; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="width: 48px; height: 48px; background: #dbeafe; border-radius: 12px; margin-bottom: 16px; display: flex; align-items: center; justify-content: center;">
                    <span style="font-size: 24px;">🚀</span>
                </div>
                <h3 style="color: #1e293b; font-size: 16px; margin: 0 0 8px; font-weight: 600;">Feature One</h3>
                <p style="color: #64748b; font-size: 14px; margin: 0; line-height: 1.5;">Description of the first feature or benefit.</p>
            </div>
            <div style="flex: 1; background: #ffffff; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="width: 48px; height: 48px; background: #fce7f3; border-radius: 12px; margin-bottom: 16px; display: flex; align-items: center; justify-content: center;">
                    <span style="font-size: 24px;">✨</span>
                </div>
                <h3 style="color: #1e293b; font-size: 16px; margin: 0 0 8px; font-weight: 600;">Feature Two</h3>
                <p style="color: #64748b; font-size: 14px; margin: 0; line-height: 1.5;">Description of the second feature or benefit.</p>
            </div>
        </div>
        <div style="text-align: center; margin-top: 32px;">
            <a href="{{cta_url}}" style="display: inline-block; background: #6366f1; color: #ffffff; padding: 14px 32px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 15px;">{{cta_text}}</a>
        </div>
        <div style="text-align: center; margin-top: 40px; padding-top: 32px; border-top: 1px solid #e2e8f0;">
            <p style="color: #94a3b8; font-size: 13px; margin: 0;">© {{year}} {{app_name}}</p>
            <p style="margin: 8px 0 0;"><a href="{{unsubscribe_url}}" style="color: #94a3b8; font-size: 13px;">Unsubscribe</a></p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getNewsletterClassicTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{subject}}</title>
</head>
<body style="margin: 0; padding: 0; font-family: Georgia, 'Times New Roman', serif; background-color: #fafaf9;">
    <div style="max-width: 600px; margin: 0 auto; padding: 40px 20px;">
        <div style="background: #ffffff; border-radius: 4px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="background: #292524; padding: 32px; text-align: center;">
                <h1 style="color: #fafaf9; font-size: 24px; margin: 0; font-weight: 400; letter-spacing: 2px;">{{app_name}}</h1>
                <p style="color: #a8a29e; font-size: 12px; margin: 8px 0 0; text-transform: uppercase; letter-spacing: 1px;">Newsletter</p>
            </div>
            <div style="padding: 40px;">
                <h2 style="color: #292524; font-size: 28px; margin: 0 0 24px; font-weight: 400; line-height: 1.3;">{{subject}}</h2>
                <div style="color: #57534e; font-size: 17px; line-height: 1.8;">
                    {{content}}
                </div>
                <div style="margin-top: 32px; padding-top: 32px; border-top: 1px solid #e7e5e4;">
                    <a href="{{cta_url}}" style="color: #292524; font-size: 14px; text-decoration: underline; font-weight: 600;">{{cta_text}} →</a>
                </div>
            </div>
            <div style="background: #f5f5f4; padding: 24px; text-align: center;">
                <p style="color: #78716c; font-size: 13px; margin: 0;">© {{year}} {{app_name}}. All rights reserved.</p>
                <p style="margin: 8px 0 0;"><a href="{{unsubscribe_url}}" style="color: #78716c; font-size: 13px;">Unsubscribe</a></p>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getWelcomeSeriesTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{subject}}</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #fef3c7;">
    <div style="max-width: 600px; margin: 0 auto; padding: 40px 20px;">
        <div style="background: #ffffff; border-radius: 24px; padding: 48px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
            <div style="text-align: center; margin-bottom: 32px;">
                <div style="width: 80px; height: 80px; background: #fef3c7; border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
                    <span style="font-size: 40px;">👋</span>
                </div>
                <h1 style="color: #1e293b; font-size: 28px; margin: 0; font-weight: 700;">Welcome to {{app_name}}!</h1>
            </div>
            <div style="color: #475569; font-size: 16px; line-height: 1.7; text-align: center;">
                {{content}}
            </div>
            <div style="text-align: center; margin-top: 32px;">
                <a href="{{cta_url}}" style="display: inline-block; background: #f59e0b; color: #ffffff; padding: 16px 40px; border-radius: 50px; text-decoration: none; font-weight: 600; font-size: 16px;">{{cta_text}}</a>
            </div>
            <div style="margin-top: 40px; padding: 24px; background: #fefce8; border-radius: 12px; text-align: center;">
                <p style="color: #854d0e; font-size: 14px; margin: 0; font-weight: 500;">Need help? Reply to this email and we'll assist you!</p>
            </div>
            <div style="margin-top: 32px; padding-top: 32px; border-top: 1px solid #e2e8f0; text-align: center;">
                <p style="color: #94a3b8; font-size: 13px; margin: 0;">© {{year}} {{app_name}}</p>
                <p style="margin: 8px 0 0;"><a href="{{unsubscribe_url}}" style="color: #94a3b8; font-size: 13px;">Unsubscribe</a></p>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getSaleBlastTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{subject}}</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #fef2f2;">
    <div style="max-width: 600px; margin: 0 auto; padding: 40px 20px;">
        <div style="background: linear-gradient(135deg, #dc2626, #ef4444); border-radius: 24px; padding: 48px; text-align: center;">
            <div style="background: #ffffff; display: inline-block; padding: 8px 20px; border-radius: 50px; margin-bottom: 20px;">
                <span style="color: #dc2626; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Limited Time</span>
            </div>
            <h1 style="color: #ffffff; font-size: 48px; margin: 0 0 16px; font-weight: 800;">{{discount_percent}}% OFF</h1>
            <p style="color: rgba(255,255,255,0.9); font-size: 18px; margin: 0;">{{preview_text}}</p>
        </div>
        <div style="background: #ffffff; margin: -20px 20px 20px; border-radius: 16px; padding: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
            <div style="color: #334155; font-size: 16px; line-height: 1.7; text-align: center;">
                {{content}}
            </div>
            <div style="text-align: center; margin-top: 32px;">
                <a href="{{cta_url}}" style="display: inline-block; background: #dc2626; color: #ffffff; padding: 16px 48px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 18px; text-transform: uppercase; letter-spacing: 1px;">{{cta_text}}</a>
            </div>
            <p style="text-align: center; color: #94a3b8; font-size: 13px; margin: 20px 0 0;">Offer expires {{expiry_date}}</p>
        </div>
        <div style="text-align: center; padding: 20px;">
            <p style="color: #94a3b8; font-size: 13px; margin: 0;">© {{year}} {{app_name}}</p>
            <p style="margin: 8px 0 0;"><a href="{{unsubscribe_url}}" style="color: #94a3b8; font-size: 13px;">Unsubscribe</a></p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getProductShowcaseTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{subject}}</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc;">
    <div style="max-width: 600px; margin: 0 auto; padding: 40px 20px;">
        <div style="text-align: center; margin-bottom: 32px;">
            <h1 style="color: #1e293b; font-size: 24px; margin: 0; font-weight: 700;">{{app_name}}</h1>
        </div>
        <div style="background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="background: linear-gradient(135deg, #f8fafc, #e2e8f0); padding: 40px; text-align: center;">
                <img src="{{product_image}}" alt="Product" style="max-width: 200px; height: auto;">
            </div>
            <div style="padding: 32px;">
                <span style="display: inline-block; background: #dbeafe; color: #1d4ed8; padding: 4px 12px; border-radius: 50px; font-size: 12px; font-weight: 600; margin-bottom: 12px;">New Arrival</span>
                <h2 style="color: #1e293b; font-size: 24px; margin: 0 0 12px; font-weight: 700;">{{product_name}}</h2>
                <p style="color: #64748b; font-size: 16px; line-height: 1.6; margin: 0 0 20px;">{{content}}</p>
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
                    <span style="color: #1e293b; font-size: 28px; font-weight: 700;">{{product_price}}</span>
                    <span style="color: #94a3b8; font-size: 18px; text-decoration: line-through;">{{original_price}}</span>
                </div>
                <a href="{{cta_url}}" style="display: block; background: #6366f1; color: #ffffff; padding: 16px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 16px; text-align: center;">{{cta_text}}</a>
            </div>
        </div>
        <div style="text-align: center; margin-top: 32px;">
            <p style="color: #94a3b8; font-size: 13px; margin: 0;">© {{year}} {{app_name}}</p>
            <p style="margin: 8px 0 0;"><a href="{{unsubscribe_url}}" style="color: #94a3b8; font-size: 13px;">Unsubscribe</a></p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getSimpleTextTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{subject}}</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #ffffff;">
    <div style="max-width: 560px; margin: 0 auto; padding: 48px 20px;">
        <div style="margin-bottom: 32px;">
            <h1 style="color: #1e293b; font-size: 20px; margin: 0; font-weight: 600;">{{app_name}}</h1>
        </div>
        <div style="color: #334155; font-size: 16px; line-height: 1.8;">
            {{content}}
        </div>
        <div style="margin-top: 32px;">
            <a href="{{cta_url}}" style="color: #6366f1; font-size: 16px; font-weight: 600; text-decoration: none;">{{cta_text}} →</a>
        </div>
        <div style="margin-top: 48px; padding-top: 24px; border-top: 1px solid #e2e8f0;">
            <p style="color: #94a3b8; font-size: 13px; margin: 0;">Sent by {{app_name}}</p>
            <p style="color: #94a3b8; font-size: 13px; margin: 8px 0 0;"><a href="{{unsubscribe_url}}" style="color: #94a3b8;">Unsubscribe</a></p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getModernSplitTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{subject}}</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9;">
    <div style="max-width: 600px; margin: 0 auto; padding: 40px 20px;">
        <div style="text-align: center; margin-bottom: 24px;">
            <h1 style="color: #1e293b; font-size: 24px; margin: 0; font-weight: 700;">{{app_name}}</h1>
        </div>
        <div style="background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td width="50%" style="background: linear-gradient(180deg, #6366f1 0%, #8b5cf6 100%); padding: 48px 32px; vertical-align: middle;">
                        <h2 style="color: #ffffff; font-size: 24px; margin: 0 0 16px; font-weight: 700;">{{headline}}</h2>
                        <p style="color: rgba(255,255,255,0.85); font-size: 15px; line-height: 1.6; margin: 0;">{{preview_text}}</p>
                    </td>
                    <td width="50%" style="padding: 48px 32px; vertical-align: middle;">
                        <div style="color: #475569; font-size: 15px; line-height: 1.7;">
                            {{content}}
                        </div>
                        <div style="margin-top: 24px;">
                            <a href="{{cta_url}}" style="display: inline-block; background: #6366f1; color: #ffffff; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px;">{{cta_text}}</a>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div style="text-align: center; margin-top: 32px;">
            <p style="color: #94a3b8; font-size: 13px; margin: 0;">© {{year}} {{app_name}}. All rights reserved.</p>
            <p style="margin: 8px 0 0;"><a href="{{unsubscribe_url}}" style="color: #94a3b8; font-size: 13px;">Unsubscribe</a></p>
        </div>
    </div>
</body>
</html>
HTML;
    }
}
