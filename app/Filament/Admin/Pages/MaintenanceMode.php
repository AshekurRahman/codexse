<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class MaintenanceMode extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Maintenance Mode';
    protected static ?string $title = 'Maintenance Mode';
    protected static ?int $navigationSort = 111;

    protected static string $view = 'filament.admin.pages.maintenance-mode';

    public ?array $data = [];
    public bool $isMaintenanceMode = false;

    public function mount(): void
    {
        $this->isMaintenanceMode = app()->isDownForMaintenance();

        $this->form->fill([
            'maintenance_message' => Setting::get('maintenance_message', 'We are currently performing scheduled maintenance. Please check back soon.'),
            'maintenance_retry' => Setting::get('maintenance_retry', 60),
            'maintenance_secret' => Setting::get('maintenance_secret', ''),
            'allowed_ips' => Setting::get('maintenance_allowed_ips', ''),
            'maintenance_scheduled_end' => Setting::get('maintenance_scheduled_end', ''),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Maintenance Settings')
                    ->description('Configure the maintenance mode message and options')
                    ->schema([
                        Forms\Components\Textarea::make('maintenance_message')
                            ->label('Maintenance Message')
                            ->rows(3)
                            ->placeholder('We are currently performing scheduled maintenance...')
                            ->helperText('This message will be shown to visitors during maintenance'),

                        Forms\Components\TextInput::make('maintenance_retry')
                            ->label('Retry After (seconds)')
                            ->numeric()
                            ->default(60)
                            ->minValue(30)
                            ->maxValue(86400)
                            ->helperText('Tell search engines when to retry (in seconds)'),

                        Forms\Components\DateTimePicker::make('maintenance_scheduled_end')
                            ->label('Scheduled End Time')
                            ->helperText('Optional: When maintenance is expected to end'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Access Control')
                    ->description('Control who can access the site during maintenance')
                    ->schema([
                        Forms\Components\TextInput::make('maintenance_secret')
                            ->label('Secret Bypass Token')
                            ->placeholder('my-secret-key')
                            ->helperText('Users can bypass maintenance by visiting: /secret-key')
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('generate')
                                    ->icon('heroicon-o-arrow-path')
                                    ->action(function (Forms\Set $set) {
                                        $set('maintenance_secret', bin2hex(random_bytes(16)));
                                    })
                            ),

                        Forms\Components\Textarea::make('allowed_ips')
                            ->label('Allowed IP Addresses')
                            ->rows(3)
                            ->placeholder("192.168.1.1\n10.0.0.1")
                            ->helperText('One IP address per line. These IPs can access the site during maintenance.'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::set('maintenance_message', $data['maintenance_message'] ?? '', 'maintenance', 'text', false);
        Setting::set('maintenance_retry', $data['maintenance_retry'] ?? 60, 'maintenance', 'integer', false);
        Setting::set('maintenance_secret', $data['maintenance_secret'] ?? '', 'maintenance', 'string', true);
        Setting::set('maintenance_allowed_ips', $data['allowed_ips'] ?? '', 'maintenance', 'text', false);
        Setting::set('maintenance_scheduled_end', $data['maintenance_scheduled_end'] ?? '', 'maintenance', 'datetime', false);

        Notification::make()
            ->title('Maintenance settings saved')
            ->success()
            ->send();
    }

    public function enableMaintenance(): void
    {
        $data = $this->form->getState();
        $secret = $data['maintenance_secret'] ?? '';
        $retry = $data['maintenance_retry'] ?? 60;

        // Save settings first
        $this->save();

        // Build artisan command options
        $options = [
            '--retry' => $retry,
        ];

        if (!empty($secret)) {
            $options['--secret'] = $secret;
        }

        try {
            Artisan::call('down', $options);

            // Create custom maintenance page
            $this->createMaintenancePage();

            $this->isMaintenanceMode = true;

            Notification::make()
                ->title('Maintenance mode enabled')
                ->body($secret ? 'Bypass URL: ' . url($secret) : null)
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to enable maintenance mode')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function disableMaintenance(): void
    {
        try {
            Artisan::call('up');

            $this->isMaintenanceMode = false;

            Notification::make()
                ->title('Maintenance mode disabled')
                ->body('Your site is now accessible to all visitors.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to disable maintenance mode')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    private function createMaintenancePage(): void
    {
        $message = Setting::get('maintenance_message', 'We are currently performing scheduled maintenance. Please check back soon.');
        $scheduledEnd = Setting::get('maintenance_scheduled_end', '');

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance - Codexse</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 60px 40px;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            text-align: center;
            max-width: 500px;
        }
        .icon {
            width: 80px;
            height: 80px;
            background: #f0f4ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
        }
        .icon svg {
            width: 40px;
            height: 40px;
            color: #667eea;
        }
        h1 {
            color: #1f2937;
            font-size: 28px;
            margin-bottom: 15px;
        }
        p {
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .scheduled {
            background: #f0f4ff;
            padding: 15px 20px;
            border-radius: 10px;
            color: #667eea;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </div>
        <h1>Under Maintenance</h1>
        <p>{$message}</p>
HTML;

        if ($scheduledEnd) {
            $formattedDate = date('F j, Y \a\t g:i A', strtotime($scheduledEnd));
            $html .= "<div class=\"scheduled\">Expected back: {$formattedDate}</div>";
        }

        $html .= <<<HTML
    </div>
</body>
</html>
HTML;

        // Write to the storage/framework directory
        File::put(storage_path('framework/maintenance.php'), '<?php return ' . var_export(['message' => $message], true) . ';');
    }

    public static function getNavigationBadge(): ?string
    {
        return app()->isDownForMaintenance() ? 'ON' : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return app()->isDownForMaintenance() ? 'danger' : null;
    }
}
