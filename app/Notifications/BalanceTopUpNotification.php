<?php

namespace App\Notifications;

use App\Models\NotificationPreference;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BalanceTopUpNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $transaction;

    /**
     * Create a new notification instance.
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $preference = $notifiable->notificationPreferences()->where('type', 'balance_topup')->first();
        $channels = [];
        if (!$preference || $preference->database_enabled) {
            $channels[] = 'database';
        }
        if (!$preference || $preference->email_enabled) {
            $channels[] = 'mail';
        }
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = "Top-up Saldo Berhasil - Rp " . number_format($this->transaction->amount, 0, ',', '.');
        $greeting = "Halo " . $notifiable->name . ",";
        $line1 = "Top-up saldo Anda sebesar Rp " . number_format($this->transaction->amount, 0, ',', '.') . " telah berhasil diproses.";
        $line2 = "Saldo Anda sekarang: Rp " . number_format($notifiable->balance, 0, ',', '.');

        return (new MailMessage)
                    ->subject($subject)
                    ->greeting($greeting)
                    ->line($line1)
                    ->line($line2)
                    ->action('Lihat Saldo', route('balance.index'))
                    ->line('Terima kasih telah menggunakan aplikasi kami!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'transaction_id' => $this->transaction->id,
            'transaction_uuid' => $this->transaction->uuid,
            'amount' => $this->transaction->amount,
            'new_balance' => $notifiable->balance,
            'url' => route('balance.index'),
        ];
    }
}
