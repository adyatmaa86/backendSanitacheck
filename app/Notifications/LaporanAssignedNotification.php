<?php

namespace App\Notifications;

use App\Models\Laporan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LaporanAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Laporan $laporan;

    public function __construct(Laporan $laporan)
    {
        $this->laporan = $laporan;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $facilityName = $this->laporan->facility->nama_fasilitas ?? 'Fasilitas';

        return [
            'laporan_id' => $this->laporan->id,
            'facility_name' => $facilityName,
            'message' => "Anda ditugaskan menangani laporan dari {$this->laporan->nama_pelapor} untuk {$facilityName}. Segera tindak lanjuti.",
        ];
    }
}
