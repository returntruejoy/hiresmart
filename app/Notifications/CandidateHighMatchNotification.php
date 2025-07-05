<?php

namespace App\Notifications;

use App\Models\JobMatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CandidateHighMatchNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public JobMatch $match)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $jobPost = $this->match->jobPost;
        $score = $this->match->match_score;

        return (new MailMessage)
            ->subject("You're a great match for a new job!")
            ->greeting("Hi {$notifiable->name},")
            ->line("We found a new job opportunity that looks like a great fit for you (Match Score: {$score}%).")
            ->line("Job Title: **{$jobPost->title}** at **{$jobPost->company_name}**")
            ->line("Location: **{$jobPost->location}**")
            ->action('View Job & Apply', url('/api/v1/job-posts/'.$jobPost->id))
            ->line('Thank you for using HireSmart!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'job_post_id' => $this->match->job_post_id,
            'match_score' => $this->match->match_score,
        ];
    }
}
