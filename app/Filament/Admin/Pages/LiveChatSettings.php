<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class LiveChatSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.admin.pages.live-chat-settings';

    protected static ?string $navigationLabel = 'Chat Settings';

    protected static ?string $title = 'Live Chat Settings';

    protected static ?string $navigationGroup = 'Support';

    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'live_chat_enabled' => Setting::get('live_chat_enabled', true),
            'live_chat_welcome_message' => Setting::get('live_chat_welcome_message', 'Welcome! A support agent will be with you shortly.'),
            'live_chat_offline_message' => Setting::get('live_chat_offline_message', 'We\'re currently offline. Leave a message and we\'ll get back to you.'),
            'live_chat_departments' => Setting::get('live_chat_departments', ['general', 'sales', 'technical', 'billing']),
            'live_chat_auto_greeting' => Setting::get('live_chat_auto_greeting', true),
            'live_chat_require_email' => Setting::get('live_chat_require_email', true),
            'live_chat_sound_notifications' => Setting::get('live_chat_sound_notifications', true),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('General Settings')
                    ->description('Configure basic live chat functionality')
                    ->schema([
                        Toggle::make('live_chat_enabled')
                            ->label('Enable Live Chat')
                            ->helperText('Show the live chat widget on your site')
                            ->default(true),

                        Toggle::make('live_chat_auto_greeting')
                            ->label('Auto Greeting')
                            ->helperText('Automatically send a welcome message when chat starts'),

                        Toggle::make('live_chat_require_email')
                            ->label('Require Email for Guests')
                            ->helperText('Guest users must provide their email before starting a chat'),

                        Toggle::make('live_chat_sound_notifications')
                            ->label('Sound Notifications')
                            ->helperText('Play sound when new messages arrive'),
                    ])
                    ->columns(2),

                Section::make('Messages')
                    ->description('Customize chat messages')
                    ->schema([
                        Textarea::make('live_chat_welcome_message')
                            ->label('Welcome Message')
                            ->helperText('Message shown when a chat starts')
                            ->rows(3),

                        Textarea::make('live_chat_offline_message')
                            ->label('Offline Message')
                            ->helperText('Message shown when no agents are available')
                            ->rows(3),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label('Save Settings')
                ->submit('save'),
        ];
    }
}
