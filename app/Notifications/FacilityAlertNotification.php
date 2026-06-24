<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FacilityAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $facility;
    protected $action;

    /**
     * Create a new notification instance.
     */
    public function __construct(\App\Models\Fasilitas $facility, string $action)
    {
        $this->facility = $facility;
        $this->action = $action;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'facility_id' => $this->facility->id,
            'facility_name' => $this->facility->nama_fasilitas,
            'action' => $this->action,
            'message' => "Fasilitas {$this->facility->nama_fasilitas} telah {$this->action} oleh Admin.",
        ];
    }
}
