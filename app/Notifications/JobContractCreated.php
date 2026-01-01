<?php

namespace App\Notifications;

use App\Models\JobContract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobContractCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public JobContract $contract
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Contract Started - ' . $this->contract->title)
            ->view('emails.job.contract-started', [
                'contract' => $this->contract,
                'job' => $this->contract->jobPosting,
                'client' => $this->contract->client,
                'freelancer' => $this->contract->seller->user,
                'recipientEmail' => $notifiable->email,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'job_contract_created',
            'contract_id' => $this->contract->id,
            'job_id' => $this->contract->job_posting_id,
            'title' => $this->contract->title,
            'amount' => $this->contract->total_amount,
            'message' => 'Contract started: ' . $this->contract->title,
            'url' => '/contracts/' . $this->contract->id,
        ];
    }
}
