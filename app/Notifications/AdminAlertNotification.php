<?php

namespace App\Notifications;

use App\Models\Winner;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public ?Winner $winner = null,
        public string $alertType = 'winner'
    ) {}

    /**
     * Get the notification's delivery channels.
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
        return match ($this->alertType) {
            'winner' => $this->winnerAlert(),
            'low_budget' => $this->lowBudgetAlert(),
            default => $this->defaultAlert(),
        };
    }

    /**
     * Winner alert email
     */
    protected function winnerAlert(): MailMessage
    {
        $user = $this->winner->user;
        $question = $this->winner->dailyQuestion;

        return (new MailMessage)
            ->subject('🏆 New Contest Winner!')
            ->greeting('New Winner Alert')
            ->line("**Winner:** {$user->name} ({$user->email})")
            ->line("**Prize Amount:** $" . $this->winner->prize_amount)
            ->line("**Question:** {$question->question_text}")
            ->line("**Answer:** {$question->correct_answer}")
            ->line("**Submitted At:** {$this->winner->submission->submitted_at}")
            ->action('View in Admin Panel', url('/admin/winners/' . $this->winner->id))
            ->line('Gift card delivery has been queued.');
    }

    /**
     * Low budget alert email
     */
    protected function lowBudgetAlert(): MailMessage
    {
        $currentMonth = now()->startOfMonth()->toDateString();
        $pool = \App\Models\PrizePool::where('month', $currentMonth)->first();

        return (new MailMessage)
            ->subject('⚠️ Low Prize Pool Balance')
            ->greeting('Budget Alert')
            ->line("The prize pool for {$currentMonth} is running low.")
            ->lineIf($pool, "**Current Balance:** $" . ($pool?->remaining ?? 0))
            ->lineIf($pool, "**Budget:** $" . ($pool?->budget ?? 0))
            ->lineIf($pool, "**Spent:** $" . ($pool?->spent ?? 0))
            ->action('Manage Budget', url('/admin/prize-pools'))
            ->line('Please add funds or adjust contest settings to avoid interruption.');
    }

    /**
     * Default alert email
     */
    protected function defaultAlert(): MailMessage
    {
        return (new MailMessage)
            ->subject('Admin Alert')
            ->line('An admin notification was triggered.')
            ->action('View Admin Panel', url('/admin'));
    }
}
