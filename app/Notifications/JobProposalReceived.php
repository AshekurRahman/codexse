<?php

namespace App\Notifications;

use App\Models\JobProposal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobProposalReceived extends Notification implements ShouldQueue
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
            ->subject('New Proposal Received - ' . $this->proposal->jobPosting->title)
            ->view('emails.job.proposal-received', [
                'job' => $this->proposal->jobPosting,
                'proposal' => $this->proposal,
                'recipientEmail' => $notifiable->email,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'job_proposal_received',
            'proposal_id' => $this->proposal->id,
            'job_id' => $this->proposal->job_posting_id,
            'job_title' => $this->proposal->jobPosting->title,
            'freelancer_name' => $this->proposal->seller->user->name,
            'proposed_price' => $this->proposal->proposed_price,
            'message' => 'New proposal from ' . $this->proposal->seller->user->name,
            'url' => '/jobs/' . $this->proposal->jobPosting->slug . '/proposals',
        ];
    }
}
