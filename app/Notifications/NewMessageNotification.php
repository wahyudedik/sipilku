<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $preference = \App\Models\NotificationPreference::getUserPreference($notifiable->id, 'message');
        $channels = [];

        if ($preference->database_enabled) {
            $channels[] = 'database';
        }

        if ($preference->email_enabled) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Pesan Baru dari ' . $this->message->sender->name)
                    ->line('Anda menerima pesan baru dari ' . $this->message->sender->name)
                    ->line(Str::limit($this->message->message, 100))
                    ->action('Lihat Pesan', route('messages.chat', $this->message->sender))
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
            'message_id' => $this->message->id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'message_preview' => Str::limit($this->message->message, 100),
            'has_attachments' => !empty($this->message->attachments),
            'created_at' => $this->message->created_at->toIso8601String(),
        ];
    }
}

