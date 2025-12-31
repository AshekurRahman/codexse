<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EmailCampaignResource\Pages;
use App\Jobs\ProcessEmailCampaignJob;
use App\Models\EmailCampaign;
use App\Models\EmailTemplate;
use App\Models\NewsletterSubscriber;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmailCampaignResource extends Resource
{
    protected static ?string $model = EmailCampaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?string $navigationLabel = 'Email Campaigns';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Campaign')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Content')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Section::make('Campaign Details')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Campaign Name')
                                            ->placeholder('e.g., January Newsletter, Product Launch')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('subject')
                                            ->label('Email Subject')
                                            ->placeholder('e.g., Check out our latest products!')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('preview_text')
                                            ->label('Preview Text')
                                            ->placeholder('This text appears in email clients as a preview...')
                                            ->helperText('The preview text that appears in email clients before opening the email.')
                                            ->rows(2)
                                            ->maxLength(255),
                                    ])
                                    ->columns(1),

                                Forms\Components\Section::make('Email Template')
                                    ->schema([
                                        Forms\Components\Select::make('email_template_id')
                                            ->label('Select Template')
                                            ->options(EmailTemplate::active()->orderBy('sort_order')->pluck('name', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Choose a pre-designed template or leave empty to use custom content.')
                                            ->live()
                                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                if ($state) {
                                                    $template = EmailTemplate::find($state);
                                                    if ($template) {
                                                        Notification::make()
                                                            ->title('Template Selected')
                                                            ->body("'{$template->name}' template will be used for this campaign.")
                                                            ->success()
                                                            ->send();
                                                    }
                                                }
                                            }),
                                        Forms\Components\Placeholder::make('template_preview')
                                            ->label('Template Preview')
                                            ->content(function ($get) {
                                                $templateId = $get('email_template_id');
                                                if (!$templateId) {
                                                    return 'No template selected. Custom content will be used.';
                                                }
                                                $template = EmailTemplate::find($templateId);
                                                return $template ? $template->description : '';
                                            })
                                            ->visible(fn ($get) => $get('email_template_id')),
                                    ]),

                                Forms\Components\Section::make('Email Content')
                                    ->schema([
                                        Forms\Components\RichEditor::make('content')
                                            ->label('Email Body')
                                            ->required()
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'underline',
                                                'strike',
                                                'link',
                                                'orderedList',
                                                'bulletList',
                                                'h2',
                                                'h3',
                                                'blockquote',
                                                'codeBlock',
                                                'redo',
                                                'undo',
                                            ])
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Sending Controls')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Forms\Components\Section::make('Daily Sending Limits')
                                    ->description('Control how many emails are sent each day.')
                                    ->schema([
                                        Forms\Components\TextInput::make('daily_limit')
                                            ->label('Daily Email Limit')
                                            ->numeric()
                                            ->default(100)
                                            ->minValue(1)
                                            ->maxValue(10000)
                                            ->required()
                                            ->helperText('Maximum number of emails to send per day.'),
                                        Forms\Components\TextInput::make('daily_increment')
                                            ->label('Daily Increment (Optional)')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(1000)
                                            ->helperText('Increase the daily limit by this amount each day. E.g., Day 1: 100, Day 2: 150 (if increment is 50).'),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Campaign Duration')
                                    ->description('Define how long the campaign will run.')
                                    ->schema([
                                        Forms\Components\TextInput::make('sending_duration_days')
                                            ->label('Sending Duration (Days)')
                                            ->numeric()
                                            ->default(1)
                                            ->minValue(1)
                                            ->maxValue(365)
                                            ->required()
                                            ->helperText('Total number of days the campaign will run.'),
                                        Forms\Components\DatePicker::make('scheduled_at')
                                            ->label('Start Date (Optional)')
                                            ->helperText('Leave empty to start immediately when activated.')
                                            ->minDate(now())
                                            ->native(false),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Sending Estimate')
                                    ->schema([
                                        Forms\Components\Placeholder::make('estimate')
                                            ->label('')
                                            ->content(function ($get) {
                                                $dailyLimit = (int) ($get('daily_limit') ?? 100);
                                                $increment = (int) ($get('daily_increment') ?? 0);
                                                $days = (int) ($get('sending_duration_days') ?? 1);
                                                $subscribers = NewsletterSubscriber::active()->count();

                                                $totalCapacity = 0;
                                                for ($i = 0; $i < $days; $i++) {
                                                    $totalCapacity += $dailyLimit + ($increment * $i);
                                                }

                                                $willComplete = $totalCapacity >= $subscribers;
                                                $estimatedDays = 0;
                                                $runningTotal = 0;
                                                for ($i = 0; $i < 365; $i++) {
                                                    $runningTotal += $dailyLimit + ($increment * $i);
                                                    if ($runningTotal >= $subscribers) {
                                                        $estimatedDays = $i + 1;
                                                        break;
                                                    }
                                                }

                                                return view('filament.components.sending-estimate', [
                                                    'subscribers' => $subscribers,
                                                    'dailyLimit' => $dailyLimit,
                                                    'increment' => $increment,
                                                    'days' => $days,
                                                    'totalCapacity' => $totalCapacity,
                                                    'willComplete' => $willComplete,
                                                    'estimatedDays' => $estimatedDays ?: '365+',
                                                ]);
                                            }),
                                    ]),
                            ])
                            ->visible(fn ($record) => !$record || $record->isDraft() || $record->isIdle()),
                    ])
                    ->columnSpanFull(),

                Forms\Components\Hidden::make('created_by')
                    ->default(fn () => auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Campaign')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->subject),
                Tables\Columns\TextColumn::make('template.name')
                    ->label('Template')
                    ->placeholder('Custom')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'draft',
                        'warning' => 'scheduled',
                        'info' => 'sending',
                        'success' => 'sent',
                        'danger' => 'failed',
                    ]),
                Tables\Columns\BadgeColumn::make('sending_status')
                    ->label('Sending')
                    ->colors([
                        'gray' => 'idle',
                        'success' => 'running',
                        'warning' => 'paused',
                        'primary' => 'completed',
                        'danger' => 'stopped',
                    ]),
                Tables\Columns\TextColumn::make('progress')
                    ->label('Progress')
                    ->getStateUsing(fn ($record) => "{$record->sent_count}/{$record->total_recipients}")
                    ->description(fn ($record) => $record->progress_percent . '% complete'),
                Tables\Columns\TextColumn::make('today_sent_count')
                    ->label('Today')
                    ->getStateUsing(fn ($record) => "{$record->today_sent_count}/{$record->today_limit}")
                    ->color(fn ($record) => $record->today_sent_count >= $record->today_limit ? 'warning' : 'success'),
                Tables\Columns\TextColumn::make('daily_limit')
                    ->label('Daily Limit')
                    ->numeric(),
                Tables\Columns\TextColumn::make('current_day')
                    ->label('Day')
                    ->getStateUsing(fn ($record) => "Day {$record->current_day}/{$record->sending_duration_days}"),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                        'sending' => 'Sending',
                        'sent' => 'Sent',
                        'failed' => 'Failed',
                    ]),
                Tables\Filters\SelectFilter::make('sending_status')
                    ->options([
                        'idle' => 'Idle',
                        'running' => 'Running',
                        'paused' => 'Paused',
                        'completed' => 'Completed',
                        'stopped' => 'Stopped',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->visible(fn ($record) => $record->isDraft() || $record->isIdle()),

                    // Send Test Email
                    Tables\Actions\Action::make('sendTest')
                        ->label('Send Test')
                        ->icon('heroicon-o-envelope')
                        ->color('gray')
                        ->form([
                            Forms\Components\TextInput::make('test_email')
                                ->label('Email Address')
                                ->email()
                                ->required()
                                ->default(fn () => auth()->user()->email)
                                ->helperText('Send a test email to preview this campaign.'),
                        ])
                        ->action(function ($record, array $data) {
                            try {
                                // Create a temporary subscriber for testing
                                $testSubscriber = new NewsletterSubscriber([
                                    'id' => 0,
                                    'email' => $data['test_email'],
                                    'token' => 'test-' . uniqid(),
                                ]);

                                \Illuminate\Support\Facades\Mail::to($data['test_email'])
                                    ->send(new \App\Mail\CampaignMail($record, $testSubscriber));

                                Notification::make()
                                    ->title('Test email sent')
                                    ->body("A test email has been sent to {$data['test_email']}")
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Failed to send test email')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),

                    // Start Campaign
                    Tables\Actions\Action::make('start')
                        ->label('Start Campaign')
                        ->icon('heroicon-o-play')
                        ->color('success')
                        ->visible(fn ($record) => ($record->isDraft() || $record->isIdle()) && !$record->isRunning())
                        ->requiresConfirmation()
                        ->modalHeading('Start Campaign')
                        ->modalDescription(fn ($record) => "Start sending \"{$record->name}\" to subscribers? Daily limit: {$record->daily_limit} emails/day for {$record->sending_duration_days} days.")
                        ->modalSubmitActionLabel('Start Sending')
                        ->action(function ($record) {
                            $subscriberCount = NewsletterSubscriber::active()->count();

                            if ($subscriberCount === 0) {
                                Notification::make()
                                    ->title('No active subscribers')
                                    ->body('There are no active newsletter subscribers to send to.')
                                    ->warning()
                                    ->send();
                                return;
                            }

                            $record->start();
                            dispatch(new ProcessEmailCampaignJob($record));

                            Notification::make()
                                ->title('Campaign started')
                                ->body("Sending to {$subscriberCount} subscribers. Daily limit: {$record->daily_limit}")
                                ->success()
                                ->send();
                        }),

                    // Pause Campaign
                    Tables\Actions\Action::make('pause')
                        ->label('Pause')
                        ->icon('heroicon-o-pause')
                        ->color('warning')
                        ->visible(fn ($record) => $record->isRunning())
                        ->requiresConfirmation()
                        ->action(function ($record) {
                            $record->pause();

                            Notification::make()
                                ->title('Campaign paused')
                                ->body('You can resume sending at any time.')
                                ->warning()
                                ->send();
                        }),

                    // Resume Campaign
                    Tables\Actions\Action::make('resume')
                        ->label('Resume')
                        ->icon('heroicon-o-play')
                        ->color('success')
                        ->visible(fn ($record) => $record->isPaused())
                        ->requiresConfirmation()
                        ->action(function ($record) {
                            $record->resume();
                            dispatch(new ProcessEmailCampaignJob($record));

                            Notification::make()
                                ->title('Campaign resumed')
                                ->body('Sending will continue.')
                                ->success()
                                ->send();
                        }),

                    // Stop Campaign
                    Tables\Actions\Action::make('stop')
                        ->label('Stop')
                        ->icon('heroicon-o-stop')
                        ->color('danger')
                        ->visible(fn ($record) => $record->isRunning() || $record->isPaused())
                        ->requiresConfirmation()
                        ->modalHeading('Stop Campaign')
                        ->modalDescription('Are you sure you want to stop this campaign? This action cannot be undone.')
                        ->modalSubmitActionLabel('Stop Campaign')
                        ->action(function ($record) {
                            $record->stop();

                            Notification::make()
                                ->title('Campaign stopped')
                                ->body('The campaign has been permanently stopped.')
                                ->danger()
                                ->send();
                        }),

                    Tables\Actions\Action::make('duplicate')
                        ->label('Duplicate')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('gray')
                        ->action(function ($record) {
                            $newCampaign = $record->replicate();
                            $newCampaign->name = $record->name . ' (Copy)';
                            $newCampaign->status = 'draft';
                            $newCampaign->sending_status = 'idle';
                            $newCampaign->total_recipients = 0;
                            $newCampaign->sent_count = 0;
                            $newCampaign->failed_count = 0;
                            $newCampaign->opened_count = 0;
                            $newCampaign->clicked_count = 0;
                            $newCampaign->current_day = 0;
                            $newCampaign->today_sent_count = 0;
                            $newCampaign->last_send_date = null;
                            $newCampaign->campaign_start_date = null;
                            $newCampaign->campaign_end_date = null;
                            $newCampaign->scheduled_at = null;
                            $newCampaign->sent_at = null;
                            $newCampaign->completed_at = null;
                            $newCampaign->paused_at = null;
                            $newCampaign->stopped_at = null;
                            $newCampaign->sending_log = null;
                            $newCampaign->created_by = auth()->id();
                            $newCampaign->save();

                            Notification::make()
                                ->title('Campaign duplicated')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\DeleteAction::make()
                        ->visible(fn ($record) => $record->isDraft() || $record->isStopped()),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Campaign Status')
                    ->schema([
                        Infolists\Components\Grid::make(5)
                            ->schema([
                                Infolists\Components\TextEntry::make('status')
                                    ->badge()
                                    ->color(fn ($state) => match ($state) {
                                        'draft' => 'gray',
                                        'scheduled' => 'warning',
                                        'sending' => 'info',
                                        'sent' => 'success',
                                        'failed' => 'danger',
                                        default => 'gray',
                                    }),
                                Infolists\Components\TextEntry::make('sending_status')
                                    ->label('Sending Status')
                                    ->badge()
                                    ->color(fn ($state) => match ($state) {
                                        'idle' => 'gray',
                                        'running' => 'success',
                                        'paused' => 'warning',
                                        'completed' => 'primary',
                                        'stopped' => 'danger',
                                        default => 'gray',
                                    }),
                                Infolists\Components\TextEntry::make('progress_percent')
                                    ->label('Progress')
                                    ->formatStateUsing(fn ($record) => "{$record->progress_percent}%"),
                                Infolists\Components\TextEntry::make('current_day')
                                    ->label('Current Day')
                                    ->formatStateUsing(fn ($record) => "Day {$record->current_day} of {$record->sending_duration_days}"),
                                Infolists\Components\TextEntry::make('days_remaining')
                                    ->label('Days Remaining'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Sending Statistics')
                    ->schema([
                        Infolists\Components\Grid::make(6)
                            ->schema([
                                Infolists\Components\TextEntry::make('total_recipients')
                                    ->label('Total Recipients'),
                                Infolists\Components\TextEntry::make('sent_count')
                                    ->label('Sent')
                                    ->color('success'),
                                Infolists\Components\TextEntry::make('failed_count')
                                    ->label('Failed')
                                    ->color('danger'),
                                Infolists\Components\TextEntry::make('today_sent_count')
                                    ->label('Sent Today')
                                    ->formatStateUsing(fn ($record) => "{$record->today_sent_count} / {$record->today_limit}"),
                                Infolists\Components\TextEntry::make('open_rate')
                                    ->label('Open Rate')
                                    ->formatStateUsing(fn ($record) => $record->open_rate . '%'),
                                Infolists\Components\TextEntry::make('delivery_rate')
                                    ->label('Delivery Rate')
                                    ->formatStateUsing(fn ($record) => $record->delivery_rate . '%'),
                            ]),
                    ])
                    ->visible(fn ($record) => $record->status !== 'draft'),

                Infolists\Components\Section::make('Sending Controls')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('daily_limit')
                                    ->label('Daily Limit'),
                                Infolists\Components\TextEntry::make('daily_increment')
                                    ->label('Daily Increment')
                                    ->formatStateUsing(fn ($state) => $state > 0 ? "+{$state}/day" : 'None'),
                                Infolists\Components\TextEntry::make('sending_duration_days')
                                    ->label('Duration (Days)'),
                                Infolists\Components\TextEntry::make('today_limit')
                                    ->label('Today\'s Limit'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Email Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Campaign Name'),
                        Infolists\Components\TextEntry::make('subject')
                            ->label('Subject Line'),
                        Infolists\Components\TextEntry::make('template.name')
                            ->label('Template')
                            ->placeholder('Custom Content'),
                        Infolists\Components\TextEntry::make('preview_text')
                            ->label('Preview Text')
                            ->placeholder('No preview text'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Email Content')
                    ->schema([
                        Infolists\Components\TextEntry::make('content')
                            ->label('')
                            ->html()
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Activity Log')
                    ->schema([
                        Infolists\Components\TextEntry::make('activity_log_display')
                            ->label('')
                            ->getStateUsing(function ($record) {
                                $logs = $record->sending_log ?? [];
                                if (empty($logs)) {
                                    return 'No activity yet.';
                                }
                                $logs = array_reverse($logs);
                                $html = '<div class="space-y-2">';
                                foreach (array_slice($logs, 0, 20) as $log) {
                                    $time = \Carbon\Carbon::parse($log['timestamp'])->format('M d, H:i:s');
                                    $html .= "<div class='text-sm'><span class='text-gray-400'>{$time}</span> - {$log['message']}</div>";
                                }
                                $html .= '</div>';
                                return $html;
                            })
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->collapsed()
                    ->visible(fn ($record) => !empty($record->sending_log)),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailCampaigns::route('/'),
            'create' => Pages\CreateEmailCampaign::route('/create'),
            'view' => Pages\ViewEmailCampaign::route('/{record}'),
            'edit' => Pages\EditEmailCampaign::route('/{record}/edit'),
        ];
    }
}
