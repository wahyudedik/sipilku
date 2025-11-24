<?php

namespace App\Notifications;

use App\Models\NotificationPreference;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $product;
    public $isApproved;

    /**
     * Create a new notification instance.
     */
    public function __construct(Product $product, bool $isApproved = true)
    {
        $this->product = $product;
        $this->isApproved = $isApproved;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $preference = NotificationPreference::getUserPreference($notifiable->id, 'product_approved');
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
        if ($this->isApproved) {
            return (new MailMessage)
                ->subject('Produk Anda Telah Disetujui')
                ->line('Produk Anda "' . $this->product->title . '" telah disetujui dan sekarang tersedia untuk dijual.')
                ->action('Lihat Produk', route('products.show', $this->product))
                ->line('Terima kasih telah menggunakan aplikasi kami!');
        } else {
            return (new MailMessage)
                ->subject('Produk Anda Ditolak')
                ->line('Produk Anda "' . $this->product->title . '" telah ditolak.')
                ->when($this->product->rejection_reason, function ($mail) {
                    return $mail->line('Alasan: ' . $this->product->rejection_reason);
                })
                ->action('Lihat Produk', route('seller.products.show', $this->product))
                ->line('Silakan perbaiki produk Anda dan kirim ulang untuk persetujuan.');
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'product_title' => $this->product->title,
            'is_approved' => $this->isApproved,
            'rejection_reason' => $this->product->rejection_reason,
            'created_at' => $this->product->updated_at->toIso8601String(),
        ];
    }
}
