<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InspectionAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $inspeksi;

    /**
     * Create a new notification instance.
     */
    public function __construct(\App\Models\Inspeksi $inspeksi)
    {
        $this->inspeksi = $inspeksi;
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
        $facilityName = $this->inspeksi->facility->nama_fasilitas ?? 'Fasilitas';
        $officerName = $this->inspeksi->officer->name ?? 'Petugas';
        
        return [
            'inspeksi_id' => $this->inspeksi->id,
            'facility_name' => $facilityName,
            'status_tindak_lanjut' => $this->inspeksi->status_tindak_lanjut,
            'kondisi_kebersihan' => $this->inspeksi->kondisi_kebersihan,
            'officer_name' => $officerName,
            'message' => "Fasilitas {$facilityName} berstatus '{$this->inspeksi->status_tindak_lanjut}' setelah diinspeksi oleh {$officerName}.",
        ];
    }
}
