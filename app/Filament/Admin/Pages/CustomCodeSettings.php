<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class CustomCodeSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-code-bracket';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Custom Code';
    protected static ?string $title = 'Custom Code Injection';
    protected static ?int $navigationSort = 105;

    protected static string $view = 'filament.admin.pages.custom-code-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'custom_head_code' => Setting::get('custom_head_code', ''),
            'custom_body_code' => Setting::get('custom_body_code', ''),
            'google_analytics_id' => Setting::get('google_analytics_id', ''),
            'facebook_pixel_id' => Setting::get('facebook_pixel_id', ''),
            'custom_css' => Setting::get('custom_css', ''),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Tracking & Analytics')
                    ->description('Add tracking IDs for popular analytics services. These will be automatically injected into the page.')
                    ->schema([
                        Forms\Components\TextInput::make('google_analytics_id')
                            ->label('Google Analytics ID')
                            ->placeholder('G-XXXXXXXXXX or UA-XXXXXXXX-X')
                            ->helperText('Your Google Analytics measurement ID (GA4) or tracking ID (Universal Analytics)')
                            ->maxLength(50),

                        Forms\Components\TextInput::make('facebook_pixel_id')
                            ->label('Facebook Pixel ID')
                            ->placeholder('XXXXXXXXXXXXXXXX')
                            ->helperText('Your Facebook Pixel ID for conversion tracking')
                            ->maxLength(50),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Custom Head Code')
                    ->description('Code added here will be inserted just before the closing </head> tag. Use for meta tags, external stylesheets, or third-party scripts.')
                    ->schema([
                        Forms\Components\Textarea::make('custom_head_code')
                            ->label('Head Code')
                            ->placeholder('<!-- Add custom meta tags, stylesheets, or scripts here -->
<script async src="https://example.com/script.js"></script>')
                            ->helperText('HTML/JavaScript code to inject into the <head> section')
                            ->rows(8),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Custom Body Code')
                    ->description('Code added here will be inserted just before the closing </body> tag. Ideal for chat widgets, analytics scripts, or tracking pixels.')
                    ->schema([
                        Forms\Components\Textarea::make('custom_body_code')
                            ->label('Body Code')
                            ->placeholder('<!-- Add chat widgets, tracking scripts, etc. -->
<script>
    // Your custom JavaScript here
</script>')
                            ->helperText('HTML/JavaScript code to inject before </body> tag')
                            ->rows(8),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Custom CSS')
                    ->description('Add custom CSS styles that will be applied site-wide.')
                    ->schema([
                        Forms\Components\Textarea::make('custom_css')
                            ->label('Custom Styles')
                            ->placeholder('/* Add custom CSS styles */
.my-custom-class {
    color: #333;
}')
                            ->helperText('CSS styles to inject into the page (without <style> tags)')
                            ->rows(8),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Save each setting
        Setting::set('custom_head_code', $this->buildHeadCode($data), 'custom_code', 'string', false);
        Setting::set('custom_body_code', $data['custom_body_code'] ?? '', 'custom_code', 'string', false);
        Setting::set('google_analytics_id', $data['google_analytics_id'] ?? '', 'custom_code', 'string', false);
        Setting::set('facebook_pixel_id', $data['facebook_pixel_id'] ?? '', 'custom_code', 'string', false);
        Setting::set('custom_css', $data['custom_css'] ?? '', 'custom_code', 'string', false);

        Notification::make()
            ->title('Custom code settings saved successfully')
            ->success()
            ->send();
    }

    /**
     * Build the complete head code including analytics
     */
    protected function buildHeadCode(array $data): string
    {
        $headCode = '';

        // Google Analytics
        if (!empty($data['google_analytics_id'])) {
            $gaId = htmlspecialchars($data['google_analytics_id']);
            $headCode .= <<<HTML
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id={$gaId}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{$gaId}');
</script>

HTML;
        }

        // Facebook Pixel
        if (!empty($data['facebook_pixel_id'])) {
            $fbId = htmlspecialchars($data['facebook_pixel_id']);
            $headCode .= <<<HTML
<!-- Facebook Pixel -->
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', '{$fbId}');
  fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
  src="https://www.facebook.com/tr?id={$fbId}&ev=PageView&noscript=1"/></noscript>

HTML;
        }

        // Custom CSS
        if (!empty($data['custom_css'])) {
            $headCode .= '<style>' . $data['custom_css'] . '</style>' . "\n";
        }

        // Custom head code
        if (!empty($data['custom_head_code'])) {
            $headCode .= $data['custom_head_code'];
        }

        return $headCode;
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
