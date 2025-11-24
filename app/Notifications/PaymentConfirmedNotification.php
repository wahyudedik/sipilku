<?php

namespace App\Notifications;

use App\Models\NotificationPreference;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $preference = NotificationPreference::getUserPreference($notifiable->id, 'payment');
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
            ->subject('Pembayaran Dikonfirmasi')
            ->line('Pembayaran untuk pesanan Anda telah dikonfirmasi.')
            ->line('Order ID: ' . $this->order->uuid)
            ->when($this->order->orderable, function ($mail) {
                return $mail->line('Item: ' . $this->order->orderable->title);
            })
            ->line('Total: Rp ' . number_format($this->order->total, 0, ',', '.'))
            ->action('Lihat Detail Pesanan', route('orders.show', $this->order))
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
            'order_id' => $this->order->id,
            'order_uuid' => $this->order->uuid,
            'orderable_title' => $this->order->orderable->title ?? 'N/A',
            'total' => $this->order->total,
            'created_at' => $this->order->updated_at->toIso8601String(),
        ];
    }
}
