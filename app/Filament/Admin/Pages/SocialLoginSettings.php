<?php

namespace App\Filament\Admin\Pages;

use App\Models\SocialLoginSetting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SocialLoginSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static string $view = 'filament.admin.pages.social-login-settings';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 20;

    protected static ?string $title = 'Social Login';

    public ?array $google = [];
    public ?array $facebook = [];
    public ?array $github = [];
    public ?array $twitter = [];

    public function mount(): void
    {
        $providers = ['google', 'facebook', 'github', 'twitter'];

        foreach ($providers as $provider) {
            $setting = SocialLoginSetting::firstOrCreate(
                ['provider' => $provider],
                ['is_enabled' => false, 'sort_order' => array_search($provider, $providers)]
            );

            $this->{$provider} = [
                'is_enabled' => $setting->is_enabled,
                'client_id' => $setting->client_id,
                'client_secret' => $setting->getRawOriginal('client_secret') ? '********' : '',
            ];
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Google')
                    ->description('Configure Google OAuth login')
                    ->icon('heroicon-o-globe-alt')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Toggle::make('google.is_enabled')
                            ->label('Enable Google Login')
                            ->helperText('Allow users to sign in with their Google account'),
                        Forms\Components\TextInput::make('google.client_id')
                            ->label('Client ID')
                            ->placeholder('Your Google OAuth Client ID')
                            ->helperText('Get this from Google Cloud Console'),
                        Forms\Components\TextInput::make('google.client_secret')
                            ->label('Client Secret')
                            ->password()
                            ->placeholder('Your Google OAuth Client Secret')
                            ->helperText('Leave empty to keep existing secret'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Facebook')
                    ->description('Configure Facebook OAuth login')
                    ->icon('heroicon-o-globe-alt')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\Toggle::make('facebook.is_enabled')
                            ->label('Enable Facebook Login')
                            ->helperText('Allow users to sign in with their Facebook account'),
                        Forms\Components\TextInput::make('facebook.client_id')
                            ->label('App ID')
                            ->placeholder('Your Facebook App ID')
                            ->helperText('Get this from Facebook Developers'),
                        Forms\Components\TextInput::make('facebook.client_secret')
                            ->label('App Secret')
                            ->password()
                            ->placeholder('Your Facebook App Secret')
                            ->helperText('Leave empty to keep existing secret'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('GitHub')
                    ->description('Configure GitHub OAuth login')
                    ->icon('heroicon-o-code-bracket')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\Toggle::make('github.is_enabled')
                            ->label('Enable GitHub Login')
                            ->helperText('Allow users to sign in with their GitHub account'),
                        Forms\Components\TextInput::make('github.client_id')
                            ->label('Client ID')
                            ->placeholder('Your GitHub OAuth App Client ID')
                            ->helperText('Get this from GitHub Developer Settings'),
                        Forms\Components\TextInput::make('github.client_secret')
                            ->label('Client Secret')
                            ->password()
                            ->placeholder('Your GitHub OAuth App Client Secret')
                            ->helperText('Leave empty to keep existing secret'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Twitter / X')
                    ->description('Configure Twitter/X OAuth login')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\Toggle::make('twitter.is_enabled')
                            ->label('Enable Twitter/X Login')
                            ->helperText('Allow users to sign in with their Twitter/X account'),
                        Forms\Components\TextInput::make('twitter.client_id')
                            ->label('API Key')
                            ->placeholder('Your Twitter API Key')
                            ->helperText('Get this from Twitter Developer Portal'),
                        Forms\Components\TextInput::make('twitter.client_secret')
                            ->label('API Secret')
                            ->password()
                            ->placeholder('Your Twitter API Secret')
                            ->helperText('Leave empty to keep existing secret'),
                    ])
                    ->columns(1),
            ]);
    }

    public function save(): void
    {
        $providers = ['google', 'facebook', 'github', 'twitter'];

        foreach ($providers as $provider) {
            $data = $this->{$provider};
            $setting = SocialLoginSetting::where('provider', $provider)->first();

            $updateData = [
                'is_enabled' => $data['is_enabled'] ?? false,
                'client_id' => $data['client_id'] ?? null,
            ];

            // Only update secret if a new one is provided (not the placeholder)
            if (!empty($data['client_secret']) && $data['client_secret'] !== '********') {
                $updateData['client_secret'] = $data['client_secret'];
            }

            if ($setting) {
                $setting->update($updateData);
            } else {
                SocialLoginSetting::create(array_merge($updateData, [
                    'provider' => $provider,
                    'sort_order' => array_search($provider, $providers),
                ]));
            }
        }

        Notification::make()
            ->title('Social login settings saved')
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
