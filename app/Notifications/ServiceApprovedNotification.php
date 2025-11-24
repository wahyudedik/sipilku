<?php

namespace App\Notifications;

use App\Models\NotificationPreference;
use App\Models\Service;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $service;
    public $isApproved;

    /**
     * Create a new notification instance.
     */
    public function __construct(Service $service, bool $isApproved = true)
    {
        $this->service = $service;
        $this->isApproved = $isApproved;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $preference = NotificationPreference::getUserPreference($notifiable->id, 'service_approved');
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
                ->subject('Jasa Anda Telah Disetujui')
                ->line('Jasa Anda "' . $this->service->title . '" telah disetujui dan sekarang tersedia untuk dijual.')
                ->action('Lihat Jasa', route('services.show', $this->service))
                ->line('Terima kasih telah menggunakan aplikasi kami!');
        } else {
            return (new MailMessage)
                ->subject('Jasa Anda Ditolak')
                ->line('Jasa Anda "' . $this->service->title . '" telah ditolak.')
                ->when($this->service->rejection_reason, function ($mail) {
                    return $mail->line('Alasan: ' . $this->service->rejection_reason);
                })
                ->action('Lihat Jasa', route('seller.services.show', $this->service))
                ->line('Silakan perbaiki jasa Anda dan kirim ulang untuk persetujuan.');
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
            'service_id' => $this->service->id,
            'service_title' => $this->service->title,
            'is_approved' => $this->isApproved,
            'rejection_reason' => $this->service->rejection_reason,
            'created_at' => $this->service->updated_at->toIso8601String(),
        ];
    }
}
