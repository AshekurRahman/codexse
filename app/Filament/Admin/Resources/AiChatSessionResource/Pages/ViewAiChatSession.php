<?php

namespace App\Filament\Admin\Resources\AiChatSessionResource\Pages;

use App\Filament\Admin\Resources\AiChatSessionResource;
use App\Models\AiChatSession;
use Filament\Actions;
use Filament\Resources\Pages\Page;

class ViewAiChatSession extends Page
{
    protected static string $resource = AiChatSessionResource::class;

    protected static string $view = 'filament.admin.resources.ai-chat-session-resource.pages.view-ai-chat-session';

    public AiChatSession $record;

    public function mount(AiChatSession $record): void
    {
        $this->record = $record->load(['user', 'messages' => fn ($q) => $q->orderBy('created_at')]);
    }

    public function getTitle(): string
    {
        return "AI Conversation: {$this->record->display_name}";
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('close')
                ->label('Close Conversation')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->action(fn () => $this->record->close())
                ->visible(fn () => $this->record->status === 'active'),
            Actions\Action::make('back')
                ->label('Back to List')
                ->url(AiChatSessionResource::getUrl('index'))
                ->color('gray'),
        ];
    }
}
