<?php

namespace App\Notifications;

use App\Models\Winner;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WinnerNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Winner $winner
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $giftCard = $this->winner->giftCard;

        $message = (new MailMessage)
            ->subject('🎉 Congratulations! You Won the Golden Question!')
            ->greeting('Congratulations ' . $notifiable->name . '!')
            ->line("You correctly answered today's Golden Question and won $" . $this->winner->prize_amount . "!")
            ->line('**Question:** ' . $this->winner->dailyQuestion->question_text);

        if ($giftCard && $giftCard->redemption_link) {
            $message->line('Your gift card is ready!')
                   ->action('Redeem Your Gift Card', $giftCard->redemption_link);
        }

        $message->line('If you have any issues redeeming your gift card, please contact Tremendous support directly through their help center.')
               ->line('Thank you for playing the Golden Question contest!');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'winner_id' => $this->winner->id,
            'question_id' => $this->winner->daily_question_id,
            'prize_amount' => $this->winner->prize_amount,
            'gift_card_id' => $this->winner->giftCard?->id,
        ];
    }
}
