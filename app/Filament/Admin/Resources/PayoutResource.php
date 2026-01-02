<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PayoutResource\Pages;
use App\Models\Payout;
use App\Models\Seller;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PayoutResource extends Resource
{
    protected static ?string $model = Payout::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 4;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payout Details')
                    ->schema([
                        Forms\Components\Select::make('seller_id')
                            ->label('Seller')
                            ->options(function () {
                                return Seller::with('user')
                                    ->where('status', 'approved')
                                    ->get()
                                    ->mapWithKeys(fn ($seller) => [
                                        $seller->id => $seller->business_name . ' (' . $seller->user->email . ')'
                                    ]);
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $seller = Seller::with('user.wallet')->find($state);
                                    $balance = $seller?->user?->wallet?->balance ?? 0;
                                    $set('available_balance', $balance);
                                }
                            }),

                        Forms\Components\Placeholder::make('available_balance')
                            ->label('Available Balance')
                            ->content(fn ($get) => '$' . number_format($get('available_balance') ?? 0, 2)),

                        Forms\Components\TextInput::make('amount')
                            ->label('Payout Amount')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->minValue(1),

                        Forms\Components\Select::make('currency')
                            ->options([
                                'USD' => 'USD',
                                'EUR' => 'EUR',
                                'GBP' => 'GBP',
                            ])
                            ->default('USD')
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'completed' => 'Completed',
                                'failed' => 'Failed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('pending')
                            ->required(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Payment Information')
                    ->schema([
                        Forms\Components\TextInput::make('stripe_transfer_id')
                            ->label('Stripe Transfer ID')
                            ->disabled(),

                        Forms\Components\TextInput::make('stripe_payout_id')
                            ->label('Stripe Payout ID')
                            ->disabled(),

                        Forms\Components\Textarea::make('failure_reason')
                            ->label('Failure Reason')
                            ->disabled()
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('seller.business_name')
                    ->label('Seller')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('seller.user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('amount')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'failed' => 'danger',
                        'cancelled' => 'gray',
                        default => 'secondary',
                    }),

                Tables\Columns\TextColumn::make('processed_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'cancelled' => 'Cancelled',
                    ]),

                Tables\Filters\SelectFilter::make('seller_id')
                    ->label('Seller')
                    ->options(fn () => Seller::pluck('business_name', 'id')),
            ])
            ->actions([
                Tables\Actions\Action::make('process')
                    ->icon('heroicon-o-play')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Process Payout')
                    ->modalDescription('This will process the payout and deduct from seller wallet. Continue?')
                    ->visible(fn (Payout $record) => $record->status === 'pending')
                    ->action(function (Payout $record) {
                        try {
                            DB::beginTransaction();

                            $seller = $record->seller;
                            $wallet = $seller->user->wallet;

                            if (!$wallet || $wallet->balance < $record->amount) {
                                throw new \Exception('Insufficient wallet balance');
                            }

                            // Deduct from wallet
                            $wallet->debit(
                                $record->amount,
                                'withdrawal',
                                "Payout #{$record->id}",
                                $record
                            );

                            $record->update([
                                'status' => 'completed',
                                'processed_at' => now(),
                                'completed_at' => now(),
                            ]);

                            DB::commit();

                            Notification::make()
                                ->title('Payout Processed')
                                ->body('The payout has been processed successfully.')
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            DB::rollBack();

                            $record->update([
                                'status' => 'failed',
                                'failure_reason' => $e->getMessage(),
                            ]);

                            Notification::make()
                                ->title('Payout Failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('cancel')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Payout $record) => $record->status === 'pending')
                    ->action(function (Payout $record) {
                        $record->update(['status' => 'cancelled']);

                        Notification::make()
                            ->title('Payout Cancelled')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('process_selected')
                        ->label('Process Selected')
                        ->icon('heroicon-o-play')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $processed = 0;
                            $failed = 0;

                            foreach ($records as $record) {
                                if ($record->status !== 'pending') continue;

                                try {
                                    DB::beginTransaction();

                                    $seller = $record->seller;
                                    $wallet = $seller->user->wallet;

                                    if (!$wallet || $wallet->balance < $record->amount) {
                                        throw new \Exception('Insufficient balance');
                                    }

                                    $wallet->debit(
                                        $record->amount,
                                        'withdrawal',
                                        "Payout #{$record->id}",
                                        $record
                                    );

                                    $record->update([
                                        'status' => 'completed',
                                        'processed_at' => now(),
                                        'completed_at' => now(),
                                    ]);

                                    DB::commit();
                                    $processed++;

                                } catch (\Exception $e) {
                                    DB::rollBack();
                                    $record->update([
                                        'status' => 'failed',
                                        'failure_reason' => $e->getMessage(),
                                    ]);
                                    $failed++;
                                }
                            }

                            Notification::make()
                                ->title('Bulk Payout Complete')
                                ->body("{$processed} processed, {$failed} failed")
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayouts::route('/'),
            'create' => Pages\CreatePayout::route('/create'),
            'edit' => Pages\EditPayout::route('/{record}/edit'),
        ];
    }
}
