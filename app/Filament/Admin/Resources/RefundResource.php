<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RefundResource\Pages;
use App\Models\Refund;
use App\Services\RefundService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RefundResource extends Resource
{
    protected static ?string $model = Refund::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 3;

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
                Forms\Components\Section::make('Refund Details')
                    ->schema([
                        Forms\Components\TextInput::make('refund_number')
                            ->label('Refund Number')
                            ->disabled(),

                        Forms\Components\Select::make('order_id')
                            ->label('Order')
                            ->relationship('order', 'order_number')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('amount')
                            ->label('Refund Amount')
                            ->numeric()
                            ->prefix('$')
                            ->required(),

                        Forms\Components\Select::make('type')
                            ->options(Refund::TYPES)
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->options(Refund::STATUSES)
                            ->required(),

                        Forms\Components\Select::make('reason')
                            ->options(Refund::REASONS)
                            ->required(),

                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Admin Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Payment Information')
                    ->schema([
                        Forms\Components\TextInput::make('payment_method')
                            ->disabled(),

                        Forms\Components\TextInput::make('stripe_refund_id')
                            ->label('Stripe Refund ID')
                            ->disabled(),

                        Forms\Components\TextInput::make('paypal_refund_id')
                            ->label('PayPal Refund ID')
                            ->disabled(),
                    ])
                    ->columns(3)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('refund_number')
                    ->label('Refund #')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('order.order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable()
                    ->url(fn (Refund $record) => OrderResource::getUrl('edit', ['record' => $record->order_id])),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'full' => 'success',
                        'partial' => 'info',
                        default => 'secondary',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'info',
                        'processing' => 'primary',
                        'completed' => 'success',
                        'failed', 'rejected' => 'danger',
                        default => 'secondary',
                    }),

                Tables\Columns\TextColumn::make('reason')
                    ->formatStateUsing(fn (string $state): string => Refund::REASONS[$state] ?? $state)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Refund::STATUSES),

                Tables\Filters\SelectFilter::make('type')
                    ->options(Refund::TYPES),

                Tables\Filters\SelectFilter::make('reason')
                    ->options(Refund::REASONS),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Refund $record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Admin Notes')
                            ->rows(2),
                    ])
                    ->action(function (Refund $record, array $data) {
                        $refundService = app(RefundService::class);
                        $refundService->approveRefund($record, auth()->id(), $data['notes'] ?? null);

                        Notification::make()
                            ->title('Refund Approved')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('process')
                    ->icon('heroicon-o-play')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Process Refund')
                    ->modalDescription('This will process the refund through the payment gateway. Continue?')
                    ->visible(fn (Refund $record) => $record->status === 'approved')
                    ->action(function (Refund $record) {
                        $refundService = app(RefundService::class);
                        $success = $refundService->processRefund($record);

                        if ($success) {
                            Notification::make()
                                ->title('Refund Processed')
                                ->body('The refund has been successfully processed.')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Refund Failed')
                                ->body('There was an error processing the refund. Check logs for details.')
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Refund $record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Rejection Reason')
                            ->required()
                            ->rows(2),
                    ])
                    ->action(function (Refund $record, array $data) {
                        $refundService = app(RefundService::class);
                        $refundService->rejectRefund($record, auth()->id(), $data['notes']);

                        Notification::make()
                            ->title('Refund Rejected')
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve_selected')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $refundService = app(RefundService::class);
                            foreach ($records as $record) {
                                if ($record->status === 'pending') {
                                    $refundService->approveRefund($record, auth()->id());
                                }
                            }
                            Notification::make()
                                ->title('Refunds Approved')
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
            'index' => Pages\ListRefunds::route('/'),
            'create' => Pages\CreateRefund::route('/create'),
            'edit' => Pages\EditRefund::route('/{record}/edit'),
        ];
    }
}
