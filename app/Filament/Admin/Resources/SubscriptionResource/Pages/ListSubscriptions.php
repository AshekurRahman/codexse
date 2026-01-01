<?php

namespace App\Filament\Admin\Resources\SubscriptionResource\Pages;

use App\Filament\Admin\Resources\SubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSubscriptions extends ListRecords
{
    protected static string $resource = SubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['active', 'trialing'])),
            'past_due' => Tab::make('Past Due')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'past_due')),
            'canceling' => Tab::make('Canceling')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('cancel_at_period_end', true)),
            'canceled' => Tab::make('Canceled')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['canceled', 'expired'])),
        ];
    }
}
