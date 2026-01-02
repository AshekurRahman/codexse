<?php

namespace App\Filament\Admin\Pages;

use App\Models\EmailTemplate;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class EmailTemplateSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Email Templates';
    protected static ?string $title = 'Email Template Settings';
    protected static ?int $navigationSort = 117;

    protected static string $view = 'filament.admin.pages.email-template-settings';

    public ?array $data = [];
    public ?string $selectedTemplate = null;
    public ?array $templateData = [];

    public function mount(): void
    {
        $this->form->fill([
            'email_from_name' => Setting::get('email_from_name', config('app.name')),
            'email_from_address' => Setting::get('email_from_address', config('mail.from.address')),
            'email_header_logo' => Setting::get('email_header_logo', ''),
            'email_footer_text' => Setting::get('email_footer_text', '© ' . date('Y') . ' ' . config('app.name') . '. All rights reserved.'),
            'email_primary_color' => Setting::get('email_primary_color', '#7c3aed'),
            'email_template_style' => Setting::get('email_template_style', 'modern'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Email Branding')
                    ->description('Customize the look and feel of all transactional emails')
                    ->schema([
                        Forms\Components\TextInput::make('email_from_name')
                            ->label('From Name')
                            ->placeholder('Your Company Name')
                            ->required(),

                        Forms\Components\TextInput::make('email_from_address')
                            ->label('From Email')
                            ->email()
                            ->placeholder('noreply@example.com')
                            ->required(),

                        Forms\Components\TextInput::make('email_header_logo')
                            ->label('Logo URL')
                            ->url()
                            ->placeholder('https://example.com/logo.png')
                            ->helperText('Logo displayed in email header'),

                        Forms\Components\ColorPicker::make('email_primary_color')
                            ->label('Primary Color')
                            ->default('#7c3aed'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Template Style')
                    ->schema([
                        Forms\Components\Select::make('email_template_style')
                            ->label('Email Style')
                            ->options([
                                'modern' => 'Modern (Clean, minimal design)',
                                'classic' => 'Classic (Traditional layout)',
                                'bold' => 'Bold (Eye-catching colors)',
                                'minimal' => 'Minimal (Text-focused)',
                            ])
                            ->default('modern'),

                        Forms\Components\Textarea::make('email_footer_text')
                            ->label('Footer Text')
                            ->rows(2)
                            ->placeholder('© 2024 Company. All rights reserved.')
                            ->helperText('Displayed at the bottom of all emails'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::set('email_from_name', $data['email_from_name'], 'email', 'string', false);
        Setting::set('email_from_address', $data['email_from_address'], 'email', 'string', false);
        Setting::set('email_header_logo', $data['email_header_logo'] ?? '', 'email', 'string', false);
        Setting::set('email_footer_text', $data['email_footer_text'] ?? '', 'email', 'text', false);
        Setting::set('email_primary_color', $data['email_primary_color'] ?? '#7c3aed', 'email', 'string', false);
        Setting::set('email_template_style', $data['email_template_style'] ?? 'modern', 'email', 'string', false);

        Notification::make()
            ->title('Email settings saved')
            ->success()
            ->send();
    }

    public function selectTemplate(string $slug): void
    {
        $this->selectedTemplate = $slug;
        $template = EmailTemplate::where('slug', $slug)->first();

        if ($template) {
            $this->templateData = [
                'subject' => $template->subject,
                'html_content' => $template->html_content,
            ];
        } else {
            // Load from defaults
            $defaults = EmailTemplate::DEFAULT_TEMPLATES[$slug] ?? null;
            if ($defaults) {
                $this->templateData = [
                    'subject' => $defaults['subject'],
                    'html_content' => $this->getDefaultTemplateContent($slug),
                ];
            }
        }
    }

    public function saveTemplate(): void
    {
        if (!$this->selectedTemplate) {
            return;
        }

        $defaults = EmailTemplate::DEFAULT_TEMPLATES[$this->selectedTemplate] ?? [];

        EmailTemplate::updateOrCreate(
            ['slug' => $this->selectedTemplate],
            [
                'name' => $defaults['name'] ?? $this->selectedTemplate,
                'subject' => $this->templateData['subject'],
                'html_content' => $this->templateData['html_content'],
                'category' => $defaults['category'] ?? 'general',
                'variables' => $defaults['variables'] ?? [],
                'is_system' => true,
                'is_active' => true,
            ]
        );

        Notification::make()
            ->title('Template saved')
            ->success()
            ->send();
    }

    public function resetTemplate(): void
    {
        if (!$this->selectedTemplate) {
            return;
        }

        EmailTemplate::where('slug', $this->selectedTemplate)->delete();

        $this->templateData = [
            'subject' => EmailTemplate::DEFAULT_TEMPLATES[$this->selectedTemplate]['subject'] ?? '',
            'html_content' => $this->getDefaultTemplateContent($this->selectedTemplate),
        ];

        Notification::make()
            ->title('Template reset to default')
            ->success()
            ->send();
    }

    protected function getDefaultTemplateContent(string $slug): string
    {
        $templates = [
            'order_confirmation' => '<h2>Thank you for your order, {{customer_name}}!</h2><p>Your order <strong>#{{order_number}}</strong> has been confirmed.</p><p><strong>Order Date:</strong> {{order_date}}</p><p><strong>Order Total:</strong> {{order_total}}</p>',
            'order_shipped' => '<h2>Great news, {{customer_name}}!</h2><p>Your order <strong>#{{order_number}}</strong> has been shipped.</p><p><strong>Tracking Number:</strong> {{tracking_number}}</p>',
            'order_delivered' => '<h2>Your order has arrived!</h2><p>Hi {{customer_name}}, your order <strong>#{{order_number}}</strong> was delivered on {{delivery_date}}.</p>',
            'welcome_email' => '<h2>Welcome to {{site_name}}, {{user_name}}!</h2><p>Thank you for joining our community.</p>',
            'password_reset' => '<h2>Password Reset Request</h2><p>Hi {{user_name}}, click the link below to reset your password:</p><p><a href="{{reset_link}}">Reset Password</a></p>',
            'seller_application_approved' => '<h2>Congratulations, {{seller_name}}!</h2><p>Your seller application for <strong>{{store_name}}</strong> has been approved!</p>',
            'service_order_new' => '<h2>You have a new service order!</h2><p>Hi {{seller_name}}, {{buyer_name}} has ordered your service.</p>',
            'video_call_scheduled' => '<h2>Video Call Scheduled</h2><p>Hi {{user_name}}, a video call has been scheduled with {{other_party}}.</p>',
        ];

        return $templates[$slug] ?? '<p>Template content goes here...</p>';
    }

    public function getTemplates(): array
    {
        return EmailTemplate::DEFAULT_TEMPLATES;
    }
}
