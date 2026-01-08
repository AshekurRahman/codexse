<?php

namespace App\Filament\Admin\Resources\SupportTicketResource\Pages;

use App\Filament\Admin\Resources\SupportTicketResource;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;

class ReplySupportTicket extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = SupportTicketResource::class;

    protected static string $view = 'filament.admin.resources.support-ticket-resource.pages.reply-support-ticket';

    public SupportTicket $record;

    public ?array $data = [];

    public function mount(SupportTicket $record): void
    {
        $this->record = $record;
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\RichEditor::make('message')
                    ->required()
                    ->label('Your Reply'),
                Forms\Components\FileUpload::make('attachment')
                    ->directory('ticket-attachments')
                    ->visibility('private')
                    ->maxSize(10240)
                    ->acceptedFileTypes([
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                        'image/webp',
                        'application/pdf',
                        'text/plain',
                        'application/zip',
                        'application/x-zip-compressed',
                    ])
                    ->helperText('Max 10MB. Allowed: Images, PDF, TXT, ZIP'),
                Forms\Components\Select::make('status')
                    ->options([
                        'open' => 'Open',
                        'in_progress' => 'In Progress',
                        'waiting' => 'Waiting for Customer',
                        'resolved' => 'Resolved',
                        'closed' => 'Closed',
                    ])
                    ->default($this->record->status)
                    ->label('Update Status'),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        TicketReply::create([
            'support_ticket_id' => $this->record->id,
            'user_id' => Auth::id(),
            'message' => $data['message'],
            'attachment' => $data['attachment'] ?? null,
            'is_staff_reply' => true,
        ]);

        if ($data['status'] !== $this->record->status) {
            $this->record->update(['status' => $data['status']]);
        }

        $this->form->fill();

        Notification::make()
            ->title('Reply sent successfully')
            ->success()
            ->send();
    }

    public function getTitle(): string
    {
        return "Reply to Ticket: {$this->record->ticket_number}";
    }
}
