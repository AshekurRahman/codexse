<?php

namespace App\Notifications;

use App\Models\JobProposal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobProposalAccepted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public JobProposal $proposal
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Proposal Has Been Accepted!')
            ->greeting('Congratulations ' . $notifiable->name . '!')
            ->line('Your proposal has been accepted.')
            ->line('Job: ' . $this->proposal->jobPosting->title)
            ->line('Client: ' . $this->proposal->jobPosting->client->name)
            ->line('Amount: $' . number_format($this->proposal->proposed_price, 2))
            ->action('View Contract', url('/seller/contracts'))
            ->line('A contract has been created. You can start working on the project now.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'job_proposal_accepted',
            'proposal_id' => $this->proposal->id,
            'job_id' => $this->proposal->job_posting_id,
            'job_title' => $this->proposal->jobPosting->title,
            'client_name' => $this->proposal->jobPosting->client->name,
            'message' => 'Your proposal was accepted: ' . $this->proposal->jobPosting->title,
            'url' => '/seller/contracts',
        ];
    }
}
