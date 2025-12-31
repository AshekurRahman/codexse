<?php

namespace App\Filament\Admin\Pages;

use App\Models\LiveChat;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View;

class LiveChatSupport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string $view = 'filament.admin.pages.live-chat-support';

    protected static ?string $navigationLabel = 'Live Chat';

    protected static ?string $title = 'Live Chat Support';

    protected static ?string $navigationGroup = 'Support';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        $count = LiveChat::where('status', 'waiting')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public function getViewData(): array
    {
        $waitingChats = LiveChat::where('status', 'waiting')
            ->with(['user', 'latestMessage'])
            ->latest()
            ->get();

        $activeChats = LiveChat::where('status', 'active')
            ->where('agent_id', auth()->id())
            ->with(['user', 'latestMessage'])
            ->latest()
            ->get();

        return [
            'waitingChats' => $waitingChats,
            'activeChats' => $activeChats,
        ];
    }
}
