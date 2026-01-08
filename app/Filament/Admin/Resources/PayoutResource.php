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
        // Show count of payouts requiring action (pending + pending_approval + approved)
        return static::getModel()::whereIn('status', [
            Payout::STATUS_PENDING,
            Payout::STATUS_PENDING_APPROVAL,
            Payout::STATUS_APPROVED,
        ])->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        // Use danger color if there are payouts pending approval
        $pendingApproval = static::getModel()::where('status', Payout::STATUS_PENDING_APPROVAL)->exists();
        return $pendingApproval ? 'danger' : 'warning';
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
                                Payout::STATUS_PENDING => 'Pending',
                                Payout::STATUS_PENDING_APPROVAL => 'Pending Approval',
                                Payout::STATUS_APPROVED => 'Approved',
                                Payout::STATUS_REJECTED => 'Rejected',
                                Payout::STATUS_PROCESSING => 'Processing',
                                Payout::STATUS_COMPLETED => 'Completed',
                                Payout::STATUS_FAILED => 'Failed',
                                Payout::STATUS_CANCELLED => 'Cancelled',
                            ])
                            ->default(Payout::STATUS_PENDING)
                            ->required(),

                        Forms\Components\Toggle::make('requires_approval')
                            ->label('Requires Approval')
                            ->disabled()
                            ->helperText('Payouts over $' . number_format(Payout::APPROVAL_THRESHOLD, 2) . ' require admin approval'),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Approval Information')
                    ->schema([
                        Forms\Components\Placeholder::make('approver_info')
                            ->label('Approved By')
                            ->content(fn (?Payout $record) => $record?->approver
                                ? $record->approver->name . ' (' . $record->approver->email . ')'
                                : 'Not approved yet')
                            ->visible(fn (?Payout $record) => $record?->approved_by !== null),

                        Forms\Components\Placeholder::make('approved_at_info')
                            ->label('Approved At')
                            ->content(fn (?Payout $record) => $record?->approved_at?->format('F j, Y \a\t g:i A'))
                            ->visible(fn (?Payout $record) => $record?->approved_at !== null),

                        Forms\Components\Textarea::make('approval_notes')
                            ->label('Approval Notes')
                            ->rows(2)
                            ->disabled()
                            ->columnSpanFull()
                            ->visible(fn (?Payout $record) => $record?->approval_notes !== null),

                        Forms\Components\Placeholder::make('rejecter_info')
                            ->label('Rejected By')
                            ->content(fn (?Payout $record) => $record?->rejecter
                                ? $record->rejecter->name . ' (' . $record->rejecter->email . ')'
                                : null)
                            ->visible(fn (?Payout $record) => $record?->rejected_by !== null),

                        Forms\Components\Placeholder::make('rejected_at_info')
                            ->label('Rejected At')
                            ->content(fn (?Payout $record) => $record?->rejected_at?->format('F j, Y \a\t g:i A'))
                            ->visible(fn (?Payout $record) => $record?->rejected_at !== null),

                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->rows(2)
                            ->disabled()
                            ->columnSpanFull()
                            ->visible(fn (?Payout $record) => $record?->rejection_reason !== null),
                    ])
                    ->columns(2)
                    ->collapsed()
                    ->visible(fn (?Payout $record) =>
                        $record?->approved_by !== null ||
                        $record?->rejected_by !== null ||
                        $record?->requires_approval
                    ),

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
                        Payout::STATUS_PENDING => 'warning',
                        Payout::STATUS_PENDING_APPROVAL => 'danger',
                        Payout::STATUS_APPROVED => 'info',
                        Payout::STATUS_REJECTED => 'gray',
                        Payout::STATUS_PROCESSING => 'info',
                        Payout::STATUS_COMPLETED => 'success',
                        Payout::STATUS_FAILED => 'danger',
                        Payout::STATUS_CANCELLED => 'gray',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        Payout::STATUS_PENDING => 'Pending',
                        Payout::STATUS_PENDING_APPROVAL => 'Awaiting Approval',
                        Payout::STATUS_APPROVED => 'Approved',
                        Payout::STATUS_REJECTED => 'Rejected',
                        Payout::STATUS_PROCESSING => 'Processing',
                        Payout::STATUS_COMPLETED => 'Completed',
                        Payout::STATUS_FAILED => 'Failed',
                        Payout::STATUS_CANCELLED => 'Cancelled',
                        default => ucfirst($state),
                    }),

                Tables\Columns\IconColumn::make('requires_approval')
                    ->label('Large')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->toggleable(),

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
                        Payout::STATUS_PENDING => 'Pending',
                        Payout::STATUS_PENDING_APPROVAL => 'Pending Approval',
                        Payout::STATUS_APPROVED => 'Approved',
                        Payout::STATUS_REJECTED => 'Rejected',
                        Payout::STATUS_PROCESSING => 'Processing',
                        Payout::STATUS_COMPLETED => 'Completed',
                        Payout::STATUS_FAILED => 'Failed',
                        Payout::STATUS_CANCELLED => 'Cancelled',
                    ]),

                Tables\Filters\TernaryFilter::make('requires_approval')
                    ->label('Large Payouts')
                    ->placeholder('All Payouts')
                    ->trueLabel('Large (Requires Approval)')
                    ->falseLabel('Standard'),

                Tables\Filters\SelectFilter::make('seller_id')
                    ->label('Seller')
                    ->options(fn () => Seller::pluck('business_name', 'id')),
            ])
            ->actions([
                // Approve action for payouts pending approval
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Payout')
                    ->modalDescription(fn (Payout $record) =>
                        "Approve payout of $" . number_format($record->amount, 2) .
                        " for " . ($record->seller?->business_name ?? 'Unknown Seller') . "?"
                    )
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Approval Notes (Optional)')
                            ->rows(2)
                            ->placeholder('Add any notes for this approval...'),
                    ])
                    ->visible(fn (Payout $record) => $record->isPendingApproval())
                    ->action(function (Payout $record, array $data) {
                        if ($record->approve(auth()->user(), $data['notes'] ?? null)) {
                            Notification::make()
                                ->title('Payout Approved')
                                ->body('The payout has been approved and is ready to be processed.')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Approval Failed')
                                ->body('Could not approve this payout. It may no longer be pending approval.')
                                ->danger()
                                ->send();
                        }
                    }),

                // Reject action for payouts pending approval
                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reject Payout')
                    ->modalDescription(fn (Payout $record) =>
                        "Reject payout of $" . number_format($record->amount, 2) .
                        " for " . ($record->seller?->business_name ?? 'Unknown Seller') . "? " .
                        "The funds will be returned to the seller's wallet."
                    )
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Rejection Reason')
                            ->required()
                            ->rows(2)
                            ->placeholder('Provide a reason for rejection...'),
                    ])
                    ->visible(fn (Payout $record) => $record->isPendingApproval())
                    ->action(function (Payout $record, array $data) {
                        if ($record->reject(auth()->user(), $data['reason'])) {
                            Notification::make()
                                ->title('Payout Rejected')
                                ->body('The payout has been rejected and funds returned to seller\'s wallet.')
                                ->warning()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Rejection Failed')
                                ->body('Could not reject this payout. It may no longer be pending approval.')
                                ->danger()
                                ->send();
                        }
                    }),

                // Process action for payouts ready to be processed
                Tables\Actions\Action::make('process')
                    ->icon('heroicon-o-play')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Process Payout')
                    ->modalDescription('This will mark the payout as completed. Continue?')
                    ->visible(fn (Payout $record) => $record->canBeProcessed())
                    ->action(function (Payout $record) {
                        try {
                            $record->update([
                                'status' => Payout::STATUS_COMPLETED,
                                'processed_at' => now(),
                                'completed_at' => now(),
                            ]);

                            \App\Services\ActivityLogService::logPayoutProcessed($record, auth()->user());

                            Notification::make()
                                ->title('Payout Processed')
                                ->body('The payout has been processed successfully.')
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            $record->update([
                                'status' => Payout::STATUS_FAILED,
                                'failure_reason' => $e->getMessage(),
                            ]);

                            Notification::make()
                                ->title('Payout Failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                // Cancel action for pending or approved payouts
                Tables\Actions\Action::make('cancel')
                    ->icon('heroicon-o-x-mark')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Cancel Payout')
                    ->modalDescription('Cancel this payout and return funds to seller\'s wallet?')
                    ->visible(fn (Payout $record) =>
                        $record->status === Payout::STATUS_PENDING ||
                        $record->status === Payout::STATUS_APPROVED
                    )
                    ->action(function (Payout $record) {
                        // Refund to wallet
                        $wallet = $record->seller->user->wallet;
                        if ($wallet) {
                            $wallet->credit(
                                $record->amount,
                                'payout_cancelled',
                                "Payout #{$record->id} cancelled",
                                $record
                            );
                        }

                        $record->update(['status' => Payout::STATUS_CANCELLED]);

                        Notification::make()
                            ->title('Payout Cancelled')
                            ->body('Funds have been returned to the seller\'s wallet.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Bulk approve payouts pending approval
                    Tables\Actions\BulkAction::make('approve_selected')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Approve Selected Payouts')
                        ->modalDescription('This will approve all selected payouts that are pending approval.')
                        ->action(function ($records) {
                            $approved = 0;
                            $skipped = 0;

                            foreach ($records as $record) {
                                if (!$record->isPendingApproval()) {
                                    $skipped++;
                                    continue;
                                }

                                if ($record->approve(auth()->user(), 'Bulk approved')) {
                                    $approved++;
                                } else {
                                    $skipped++;
                                }
                            }

                            Notification::make()
                                ->title('Bulk Approval Complete')
                                ->body("{$approved} approved, {$skipped} skipped")
                                ->success()
                                ->send();
                        }),

                    // Bulk process ready payouts
                    Tables\Actions\BulkAction::make('process_selected')
                        ->label('Process Selected')
                        ->icon('heroicon-o-play')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->modalHeading('Process Selected Payouts')
                        ->modalDescription('This will mark all selected payouts that can be processed as completed.')
                        ->action(function ($records) {
                            $processed = 0;
                            $skipped = 0;

                            foreach ($records as $record) {
                                if (!$record->canBeProcessed()) {
                                    $skipped++;
                                    continue;
                                }

                                try {
                                    $record->update([
                                        'status' => Payout::STATUS_COMPLETED,
                                        'processed_at' => now(),
                                        'completed_at' => now(),
                                    ]);

                                    \App\Services\ActivityLogService::logPayoutProcessed($record, auth()->user());
                                    $processed++;

                                } catch (\Exception $e) {
                                    $record->update([
                                        'status' => Payout::STATUS_FAILED,
                                        'failure_reason' => $e->getMessage(),
                                    ]);
                                    $skipped++;
                                }
                            }

                            Notification::make()
                                ->title('Bulk Process Complete')
                                ->body("{$processed} processed, {$skipped} skipped")
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
