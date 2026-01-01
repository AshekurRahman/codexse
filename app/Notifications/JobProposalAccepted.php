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
            ->view('emails.job.proposal-accepted', [
                'job' => $this->proposal->jobPosting,
                'proposal' => $this->proposal,
                'freelancer' => $notifiable,
                'recipientEmail' => $notifiable->email,
            ]);
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
