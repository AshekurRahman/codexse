<?php

namespace App\Filament\Admin\Pages;

use App\Models\SeoSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SeoSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static ?string $navigationLabel = 'SEO Tools';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 50;
    protected static string $view = 'filament.admin.pages.seo-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = SeoSetting::getAll();
        $this->form->fill($settings);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('SEO Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('General')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Forms\Components\Section::make('Site Identity')
                                    ->schema([
                                        Forms\Components\TextInput::make('site_name')
                                            ->label('Site Name')
                                            ->required()
                                            ->maxLength(60),
                                        Forms\Components\TextInput::make('site_tagline')
                                            ->label('Tagline')
                                            ->maxLength(120),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Default Meta Tags')
                                    ->description('These will be used when page-specific meta tags are not set.')
                                    ->schema([
                                        Forms\Components\TextInput::make('default_meta_title')
                                            ->label('Default Title')
                                            ->maxLength(70)
                                            ->helperText('Recommended: 50-60 characters'),
                                        Forms\Components\Textarea::make('default_meta_description')
                                            ->label('Default Description')
                                            ->maxLength(160)
                                            ->rows(3)
                                            ->helperText('Recommended: 150-160 characters'),
                                        Forms\Components\TextInput::make('default_meta_keywords')
                                            ->label('Default Keywords')
                                            ->helperText('Comma-separated keywords'),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Social Media')
                            ->icon('heroicon-o-share')
                            ->schema([
                                Forms\Components\Section::make('Open Graph (Facebook)')
                                    ->schema([
                                        Forms\Components\FileUpload::make('og_default_image')
                                            ->label('Default OG Image')
                                            ->image()
                                            ->directory('seo')
                                            ->helperText('Recommended: 1200x630 pixels'),
                                        Forms\Components\TextInput::make('facebook_app_id')
                                            ->label('Facebook App ID'),
                                    ]),

                                Forms\Components\Section::make('Twitter Card')
                                    ->schema([
                                        Forms\Components\TextInput::make('twitter_site')
                                            ->label('Twitter @username')
                                            ->prefix('@')
                                            ->placeholder('codexse'),
                                        Forms\Components\TextInput::make('twitter_creator')
                                            ->label('Default Author @username')
                                            ->prefix('@'),
                                    ])
                                    ->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Schema Markup')
                            ->icon('heroicon-o-code-bracket')
                            ->schema([
                                Forms\Components\Section::make('Organization Schema')
                                    ->description('Used for rich snippets in search results.')
                                    ->schema([
                                        Forms\Components\TextInput::make('organization_name')
                                            ->label('Organization Name')
                                            ->required(),
                                        Forms\Components\FileUpload::make('organization_logo')
                                            ->label('Organization Logo')
                                            ->image()
                                            ->directory('seo'),
                                        Forms\Components\TextInput::make('organization_url')
                                            ->label('Website URL')
                                            ->url(),
                                        Forms\Components\TextInput::make('organization_email')
                                            ->label('Contact Email')
                                            ->email(),
                                    ])
                                    ->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Sitemap')
                            ->icon('heroicon-o-map')
                            ->schema([
                                Forms\Components\Section::make('Sitemap Configuration')
                                    ->schema([
                                        Forms\Components\Toggle::make('sitemap_enabled')
                                            ->label('Enable XML Sitemap')
                                            ->default(true),
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Toggle::make('sitemap_include_products')
                                                    ->label('Include Products')
                                                    ->default(true),
                                                Forms\Components\Toggle::make('sitemap_include_categories')
                                                    ->label('Include Categories')
                                                    ->default(true),
                                                Forms\Components\Toggle::make('sitemap_include_sellers')
                                                    ->label('Include Seller Profiles')
                                                    ->default(true),
                                            ]),
                                        Forms\Components\Select::make('sitemap_changefreq')
                                            ->label('Change Frequency')
                                            ->options([
                                                'always' => 'Always',
                                                'hourly' => 'Hourly',
                                                'daily' => 'Daily',
                                                'weekly' => 'Weekly',
                                                'monthly' => 'Monthly',
                                                'yearly' => 'Yearly',
                                                'never' => 'Never',
                                            ])
                                            ->default('weekly'),
                                        Forms\Components\TextInput::make('sitemap_priority')
                                            ->label('Default Priority')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(1)
                                            ->step(0.1)
                                            ->default(0.8),
                                    ]),

                                Forms\Components\Section::make('Sitemap URL')
                                    ->schema([
                                        Forms\Components\Placeholder::make('sitemap_url')
                                            ->label('Your Sitemap URL')
                                            ->content(fn () => url('/sitemap.xml')),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Robots.txt')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Forms\Components\Section::make('Robots.txt Content')
                                    ->description('Control how search engines crawl your site.')
                                    ->schema([
                                        Forms\Components\Textarea::make('robots_txt')
                                            ->label('')
                                            ->rows(15)
                                            ->helperText('This content will be served at /robots.txt'),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Verification')
                            ->icon('heroicon-o-check-badge')
                            ->schema([
                                Forms\Components\Section::make('Search Engine Verification')
                                    ->description('Add verification codes for search engine webmaster tools.')
                                    ->schema([
                                        Forms\Components\TextInput::make('google_site_verification')
                                            ->label('Google Search Console')
                                            ->placeholder('Enter verification code only'),
                                        Forms\Components\TextInput::make('bing_site_verification')
                                            ->label('Bing Webmaster Tools')
                                            ->placeholder('Enter verification code only'),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            if ($value !== null) {
                $group = $this->getGroupForKey($key);
                SeoSetting::set($key, is_bool($value) ? ($value ? '1' : '0') : $value, $group);
            }
        }

        SeoSetting::clearCache();

        Notification::make()
            ->title('SEO Settings saved successfully')
            ->success()
            ->send();
    }

    protected function getGroupForKey(string $key): string
    {
        $groups = [
            'general' => ['site_name', 'site_tagline', 'default_meta_title', 'default_meta_description', 'default_meta_keywords'],
            'social' => ['og_default_image', 'twitter_site', 'twitter_creator', 'facebook_app_id'],
            'schema' => ['organization_name', 'organization_logo', 'organization_url', 'organization_email'],
            'sitemap' => ['sitemap_enabled', 'sitemap_include_products', 'sitemap_include_categories', 'sitemap_include_sellers', 'sitemap_changefreq', 'sitemap_priority'],
            'robots' => ['robots_txt'],
            'verification' => ['google_site_verification', 'bing_site_verification'],
        ];

        foreach ($groups as $group => $keys) {
            if (in_array($key, $keys)) {
                return $group;
            }
        }

        return 'general';
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
