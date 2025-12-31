<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\NewsletterSubscriberResource\Pages;
use App\Models\NewsletterSubscriber;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Response;
use Illuminate\Database\Eloquent\Collection;

class NewsletterSubscriberResource extends Resource
{
    protected static ?string $model = NewsletterSubscriber::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?string $navigationLabel = 'Newsletter Subscribers';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                        Forms\Components\DateTimePicker::make('confirmed_at'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->placeholder('Guest'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('confirmed_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Not confirmed'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
                Tables\Filters\TernaryFilter::make('confirmed')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('confirmed_at'),
                        false: fn ($query) => $query->whereNull('confirmed_at'),
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('export_selected')
                        ->label('Export Selected')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (Collection $records) {
                            return self::exportToCsv($records);
                        }),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_all')
                    ->label('Export All')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        $records = NewsletterSubscriber::all();
                        return self::exportToCsv($records);
                    }),
                Tables\Actions\Action::make('import')
                    ->label('Import')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('warning')
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('CSV File')
                            ->acceptedFileTypes(['text/csv', 'application/vnd.ms-excel', '.csv'])
                            ->required()
                            ->helperText('Upload a CSV file with columns: email, is_active (optional). First row should be headers.'),
                        Forms\Components\Toggle::make('skip_duplicates')
                            ->label('Skip duplicate emails')
                            ->default(true),
                    ])
                    ->action(function (array $data) {
                        $path = storage_path('app/public/' . $data['file']);

                        if (!file_exists($path)) {
                            \Filament\Notifications\Notification::make()
                                ->title('File not found')
                                ->danger()
                                ->send();
                            return;
                        }

                        $handle = fopen($path, 'r');
                        $headers = fgetcsv($handle);

                        $emailIndex = array_search('email', array_map('strtolower', $headers));
                        $activeIndex = array_search('is_active', array_map('strtolower', $headers));

                        if ($emailIndex === false) {
                            \Filament\Notifications\Notification::make()
                                ->title('Invalid CSV format')
                                ->body('CSV must have an "email" column.')
                                ->danger()
                                ->send();
                            fclose($handle);
                            return;
                        }

                        $imported = 0;
                        $skipped = 0;

                        while (($row = fgetcsv($handle)) !== false) {
                            $email = trim($row[$emailIndex] ?? '');

                            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $skipped++;
                                continue;
                            }

                            $exists = NewsletterSubscriber::where('email', $email)->exists();

                            if ($exists && $data['skip_duplicates']) {
                                $skipped++;
                                continue;
                            }

                            if (!$exists) {
                                NewsletterSubscriber::create([
                                    'email' => $email,
                                    'is_active' => $activeIndex !== false ? (bool)($row[$activeIndex] ?? true) : true,
                                    'confirmed_at' => now(),
                                ]);
                                $imported++;
                            }
                        }

                        fclose($handle);
                        unlink($path);

                        \Filament\Notifications\Notification::make()
                            ->title('Import completed')
                            ->body("Imported: {$imported}, Skipped: {$skipped}")
                            ->success()
                            ->send();
                    }),
            ]);
    }

    protected static function exportToCsv($records)
    {
        $filename = 'newsletter-subscribers-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($records) {
            $handle = fopen('php://output', 'w');

            // Add headers
            fputcsv($handle, ['Email', 'User', 'Is Active', 'Confirmed At', 'Subscribed At', 'Unsubscribed At']);

            foreach ($records as $record) {
                fputcsv($handle, [
                    $record->email,
                    $record->user?->name ?? 'Guest',
                    $record->is_active ? 'Yes' : 'No',
                    $record->confirmed_at?->format('Y-m-d H:i:s') ?? '',
                    $record->created_at?->format('Y-m-d H:i:s') ?? '',
                    $record->unsubscribed_at?->format('Y-m-d H:i:s') ?? '',
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNewsletterSubscribers::route('/'),
            'create' => Pages\CreateNewsletterSubscriber::route('/create'),
            'edit' => Pages\EditNewsletterSubscriber::route('/{record}/edit'),
        ];
    }
}
