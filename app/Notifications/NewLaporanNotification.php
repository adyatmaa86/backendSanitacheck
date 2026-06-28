<?php

namespace App\Notifications;

use App\Models\Laporan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewLaporanNotification extends Notification implements ShouldQueue
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
            'nama_pelapor' => $this->laporan->nama_pelapor,
            'message' => "Laporan baru dari {$this->laporan->nama_pelapor} untuk {$facilityName}: \"{$this->laporan->keluhan}\"",
        ];
    }
}
