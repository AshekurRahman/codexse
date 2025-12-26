<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ChatbotSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'AI Chatbot';
    protected static ?string $title = 'AI Chatbot Settings';
    protected static ?int $navigationSort = 101;

    protected static string $view = 'filament.admin.pages.chatbot-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'chatbot_enabled' => Setting::get('chatbot_enabled', false),
            'chatbot_mode' => Setting::get('chatbot_mode', 'faq'),
            'chatbot_api_key' => Setting::get('chatbot_api_key') ? '••••••••' : '',
            'chatbot_model' => Setting::get('chatbot_model', 'claude-sonnet-4-20250514'),
            'chatbot_max_tokens' => Setting::get('chatbot_max_tokens', 1024),
            'chatbot_system_prompt' => Setting::get('chatbot_system_prompt', ''),
            'chatbot_welcome_message' => Setting::get('chatbot_welcome_message', ''),
            'chatbot_offline_message' => Setting::get('chatbot_offline_message', ''),
            'chatbot_fallback_message' => Setting::get('chatbot_fallback_message', ''),
            'chatbot_rate_limit_per_minute' => Setting::get('chatbot_rate_limit_per_minute', 10),
            'chatbot_rate_limit_per_day' => Setting::get('chatbot_rate_limit_per_day', 100),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('General Settings')
                    ->schema([
                        Forms\Components\Toggle::make('chatbot_enabled')
                            ->label('Enable Chatbot')
                            ->helperText('Turn the chatbot widget on or off for all visitors')
                            ->live(),

                        Forms\Components\Radio::make('chatbot_mode')
                            ->label('Chatbot Mode')
                            ->options([
                                'faq' => 'FAQ Bot (Free)',
                                'ai' => 'AI Bot (Requires API Key)',
                            ])
                            ->descriptions([
                                'faq' => 'Uses predefined Q&A pairs - no API costs, works offline',
                                'ai' => 'Uses Claude AI for intelligent responses - requires Anthropic API key',
                            ])
                            ->default('faq')
                            ->live()
                            ->helperText('Choose between free FAQ-based responses or AI-powered responses'),
                    ]),

                Forms\Components\Section::make('FAQ Settings')
                    ->description('Configure the FAQ-based chatbot. Manage your FAQs in Support > Chatbot FAQs')
                    ->schema([
                        Forms\Components\Textarea::make('chatbot_fallback_message')
                            ->label('Fallback Message')
                            ->rows(2)
                            ->placeholder("I'm sorry, I don't have an answer for that. Please contact our support team.")
                            ->helperText('Message shown when no matching FAQ is found'),

                        Forms\Components\Placeholder::make('faq_link')
                            ->label('Manage FAQs')
                            ->content(fn () => new \Illuminate\Support\HtmlString(
                                '<a href="' . route('filament.admin.resources.chatbot-faqs.index') . '" class="text-primary-600 hover:underline">Go to Chatbot FAQs →</a>'
                            )),
                    ])
                    ->visible(fn (Get $get) => $get('chatbot_mode') === 'faq'),

                Forms\Components\Section::make('AI Configuration')
                    ->description('Configure your Anthropic Claude API settings. Get your API key from console.anthropic.com')
                    ->schema([
                        Forms\Components\TextInput::make('chatbot_api_key')
                            ->label('Anthropic API Key')
                            ->password()
                            ->placeholder('sk-ant-api...')
                            ->helperText('Your Anthropic API key. Leave empty to keep existing key.')
                            ->maxLength(255),

                        Forms\Components\Select::make('chatbot_model')
                            ->label('Claude Model')
                            ->options([
                                'claude-sonnet-4-20250514' => 'Claude Sonnet 4 (Recommended - Best balance)',
                                'claude-3-5-haiku-20241022' => 'Claude 3.5 Haiku (Faster, cheaper)',
                                'claude-opus-4-20250514' => 'Claude Opus 4 (Most capable, expensive)',
                            ])
                            ->default('claude-sonnet-4-20250514')
                            ->helperText('Select the Claude model for AI responses'),

                        Forms\Components\TextInput::make('chatbot_max_tokens')
                            ->label('Max Response Tokens')
                            ->numeric()
                            ->default(1024)
                            ->minValue(100)
                            ->maxValue(4096)
                            ->helperText('Maximum tokens for AI responses (100-4096)'),

                        Forms\Components\Textarea::make('chatbot_system_prompt')
                            ->label('System Prompt')
                            ->rows(6)
                            ->placeholder('You are a helpful customer support assistant...')
                            ->helperText('Instructions that define how the AI should behave. Leave empty for default.'),
                    ])
                    ->columns(1)
                    ->visible(fn (Get $get) => $get('chatbot_mode') === 'ai'),

                Forms\Components\Section::make('Rate Limiting')
                    ->description('Control usage to prevent abuse')
                    ->schema([
                        Forms\Components\TextInput::make('chatbot_rate_limit_per_minute')
                            ->label('Messages Per Minute')
                            ->numeric()
                            ->default(10)
                            ->minValue(1)
                            ->maxValue(60)
                            ->helperText('Maximum messages per minute'),

                        Forms\Components\TextInput::make('chatbot_rate_limit_per_day')
                            ->label('Messages Per Day')
                            ->numeric()
                            ->default(100)
                            ->minValue(10)
                            ->maxValue(1000)
                            ->helperText('Maximum messages per day'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Messages')
                    ->description('Customize user-facing messages')
                    ->schema([
                        Forms\Components\Textarea::make('chatbot_welcome_message')
                            ->label('Welcome Message')
                            ->rows(2)
                            ->placeholder("Hello! How can I help you today?")
                            ->helperText('First message shown when user opens the chat'),

                        Forms\Components\Textarea::make('chatbot_offline_message')
                            ->label('Offline Message')
                            ->rows(2)
                            ->placeholder('Our chatbot is currently unavailable.')
                            ->helperText('Message shown when the chatbot is disabled'),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::set('chatbot_enabled', $data['chatbot_enabled'] ?? false, 'chatbot', 'boolean');
        Setting::set('chatbot_mode', $data['chatbot_mode'] ?? 'faq', 'chatbot', 'string');
        Setting::set('chatbot_model', $data['chatbot_model'] ?? 'claude-sonnet-4-20250514', 'chatbot', 'string');
        Setting::set('chatbot_max_tokens', (int) ($data['chatbot_max_tokens'] ?? 1024), 'chatbot', 'integer');
        Setting::set('chatbot_system_prompt', $data['chatbot_system_prompt'] ?? '', 'chatbot', 'string');
        Setting::set('chatbot_welcome_message', $data['chatbot_welcome_message'] ?? '', 'chatbot', 'string');
        Setting::set('chatbot_offline_message', $data['chatbot_offline_message'] ?? '', 'chatbot', 'string');
        Setting::set('chatbot_fallback_message', $data['chatbot_fallback_message'] ?? '', 'chatbot', 'string');
        Setting::set('chatbot_rate_limit_per_minute', (int) ($data['chatbot_rate_limit_per_minute'] ?? 10), 'chatbot', 'integer');
        Setting::set('chatbot_rate_limit_per_day', (int) ($data['chatbot_rate_limit_per_day'] ?? 100), 'chatbot', 'integer');

        // Only update API key if a new one is provided (not the masked placeholder)
        if (!empty($data['chatbot_api_key']) && $data['chatbot_api_key'] !== '••••••••') {
            Setting::set('chatbot_api_key', $data['chatbot_api_key'], 'chatbot', 'string', true);
        }

        Notification::make()
            ->title('Chatbot settings saved successfully')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('save')
                ->label('Save Settings')
                ->submit('save'),
        ];
    }
}
