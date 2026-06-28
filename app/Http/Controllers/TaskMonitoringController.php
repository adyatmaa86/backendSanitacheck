<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Inspeksi;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskMonitoringController extends Controller
{
    /**
     * Display a listing of active petugas (Admin view).
     */
    public function pantauPetugas(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $search = $request->input('search');
        $query = User::where('role', 'petugas')->where('status_pengerjaan', 'aktif');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $petugas = $query->orderBy('name', 'asc')->paginate(5);

        // Map all current active inspections to each petugas
        foreach ($petugas as $p) {
            $p->active_inspections = Inspeksi::with('facility')
                ->where('petugas_id', $p->id)
                ->where('is_completed', false)
                ->orderBy('tanggal_inspeksi', 'desc')
                ->get();
        }

        if ($request->ajax()) {
            return view('admin.partials.pantau-table', compact('petugas'))->render();
        }

        return view('admin.pantau', compact('petugas', 'search'));
    }

    /**
     * Display current active task for logged in petugas.
     */
    public function tugasSaya()
    {
        if (Auth::user()->role !== 'petugas') {
            abort(403, 'Unauthorized action.');
        }

        $petugas = Auth::user();
        $activeInspections = Inspeksi::with('facility', 'laporan')
            ->where('petugas_id', $petugas->id)
            ->where('is_completed', false)
            ->orderBy('tanggal_inspeksi', 'desc')
            ->get();

        return view('petugas.tugas', compact('petugas', 'activeInspections'));
    }

    /**
     * Mark specific petugas task as complete.
     */
    public function completeTask(Request $request, $id)
    {
        if (Auth::user()->role !== 'petugas') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'foto_after'        => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'ketersediaan_air'  => 'required|in:tersedia,tidak',
            'ketersediaan_sabun'=> 'required|in:tersedia,tidak',
            'bau_tidak_sedap'   => 'required|in:ya,tidak',
            'catatan'           => 'nullable|string|max:1000',
        ]);

        $inspection = Inspeksi::where('petugas_id', Auth::id())
            ->where('is_completed', false)
            ->findOrFail($id);

        $facility = $inspection->facility;
        $fotoSelesaiPath = null;

        if ($request->hasFile('foto_after')) {
            if ($facility->foto_after) {
                $isUsed = Inspeksi::where('foto', $facility->foto_after)
                    ->orWhere('foto_selesai', $facility->foto_after)
                    ->exists();
                if (!$isUsed) {
                    Storage::disk('public')->delete($facility->foto_after);
                }
            }
            if ($facility->foto_before) {
                $isUsed = Inspeksi::where('id', '!=', $inspection->id)
                    ->where(function($q) use ($facility) {
                        $q->where('foto', $facility->foto_before)
                          ->orWhere('foto_selesai', $facility->foto_before);
                    })->exists();
                if (!$isUsed) {
                    Storage::disk('public')->delete($facility->foto_before);
                }
            }
            $fotoSelesaiPath = $request->file('foto_after')->store('facilities', 'public');
            $facility->update([
                'foto_before' => null,
                'foto_after' => $fotoSelesaiPath,
            ]);
        }

        if ($inspection->foto) {
            $isUsed = Inspeksi::where('id', '!=', $inspection->id)
                ->where(function($q) use ($inspection) {
                    $q->where('foto', $inspection->foto)
                      ->orWhere('foto_selesai', $inspection->foto);
                })->exists();
            if (!$isUsed) {
                Storage::disk('public')->delete($inspection->foto);
            }
        }

        $inspection->update([
            'is_completed'        => true,
            'status_tindak_lanjut' => 'aman',
            'kondisi_kebersihan'  => 'baik',
            'ketersediaan_air'    => $request->ketersediaan_air,
            'ketersediaan_sabun'  => $request->ketersediaan_sabun,
            'bau_tidak_sedap'     => $request->bau_tidak_sedap,
            'catatan'             => $request->catatan,
            'foto'                => null,
            'foto_selesai'        => $fotoSelesaiPath,
            'tanggal_inspeksi'    => now(),
        ]);

        // If this inspection came from a laporan, mark laporan as selesai
        if ($inspection->laporan_id) {
            Laporan::where('id', $inspection->laporan_id)
                ->where('status', 'diproses')
                ->update(['status' => 'selesai']);
        }

        $hasMoreTasks = Inspeksi::where('petugas_id', Auth::id())
            ->where('is_completed', false)
            ->exists();

        if (!$hasMoreTasks) {
            Auth::user()->update(['status_pengerjaan' => 'ready']);
        }

        return redirect()->route('petugas.tugas-saya')
            ->with('success', 'Tugas pada fasilitas ' . $inspection->facility->nama_fasilitas . ' berhasil diselesaikan.');
    }
}
