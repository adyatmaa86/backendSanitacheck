<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use App\Models\Inspeksi;
use App\Models\User;
use App\Notifications\InspectionAlertNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class InspeksiController extends Controller
{
    public function index()
    {
        if (Auth::user()->role === 'admin') {
            abort(403, 'Admin tidak memiliki akses ke form inspeksi.');
        }

        $query = Fasilitas::where('status_aktif', true)
            ->where(function ($q) {
                $q->where('penanggung_jawab', Auth::id())
                  ->orWhereHas('petugasTambahan', function ($q2) {
                      $q2->where('user_id', Auth::id());
                  });
            });
        $facilities = $query->get();
        return view('inspections.index', compact('facilities'));
    }

    public function history(Request $request)
    {
        $facilitiesQuery = Fasilitas::where('status_aktif', true);
        if (Auth::user()->role !== 'admin') {
            $facilitiesQuery->where(function ($q) {
                $q->where('penanggung_jawab', Auth::id())
                  ->orWhereHas('petugasTambahan', function ($q2) {
                      $q2->where('user_id', Auth::id());
                  });
            });
        }
        $facilities = $facilitiesQuery->get();

        $query = Inspeksi::with(['facility', 'officer']);

        if (Auth::user()->role !== 'admin') {
            $query->where('petugas_id', Auth::id());
        }

        // Filters
        if ($request->filled('fasilitas_id')) {
            $query->where('fasilitas_id', $request->fasilitas_id);
        }

        if ($request->filled('status_tindak_lanjut')) {
            $query->where('status_tindak_lanjut', $request->status_tindak_lanjut);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_inspeksi', $request->tanggal);
        }

        $inspections = $query->orderBy('tanggal_inspeksi', 'desc')->paginate(5);

        if ($request->ajax()) {
            return view('inspections.partials.history-table', compact('inspections'))->render();
        }

        return view('inspections.history', compact('inspections', 'facilities'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role === 'admin') {
            abort(403, 'Admin tidak memiliki akses ke form inspeksi.');
        }

        $request->validate([
            'fasilitas_id' => 'required|exists:fasilitas,id',
            'tanggal_inspeksi' => 'required|date',
            'kondisi_kebersihan' => 'required|in:baik,cukup,buruk',
            'ketersediaan_air' => 'required|in:tersedia,tidak',
            'ketersediaan_sabun' => 'required|in:tersedia,tidak',
            'bau_tidak_sedap' => 'required|in:ya,tidak',
            'catatan' => 'nullable|string|max:1000',
            'status_tindak_lanjut' => 'required|in:aman,perlu dibersihkan,perlu perbaikan',
            'foto_after' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $facility = Fasilitas::findOrFail($request->fasilitas_id);
        $isAuthorized = $facility->penanggung_jawab === Auth::id()
            || $facility->petugasTambahan()->where('user_id', Auth::id())->exists();
        if (!$isAuthorized) {
            abort(403, 'Anda tidak bertanggung jawab atas fasilitas ini.');
        }

        $isCompleted = $request->status_tindak_lanjut === 'aman';
        $fotoPath = null;

        if ($request->hasFile('foto_after')) {
            if ($facility->foto_before) {
                $isUsed = Inspeksi::where('foto', $facility->foto_before)
                    ->orWhere('foto_selesai', $facility->foto_before)
                    ->exists();
                if (!$isUsed) {
                    Storage::disk('public')->delete($facility->foto_before);
                }
            }
            if ($facility->foto_after) {
                $isUsed = Inspeksi::where('foto', $facility->foto_after)
                    ->orWhere('foto_selesai', $facility->foto_after)
                    ->exists();
                if (!$isUsed) {
                    Storage::disk('public')->delete($facility->foto_after);
                }
            }

            $fotoPath = $request->file('foto_after')->store('facilities', 'public');

            if ($isCompleted) {
                $facility->update([
                    'foto_before' => null,
                    'foto_after' => $fotoPath,
                ]);
            } else {
                $facility->update([
                    'foto_before' => $fotoPath,
                    'foto_after' => null,
                ]);
            }
        }

        $inspeksi = Inspeksi::create([
            'fasilitas_id' => $request->fasilitas_id,
            'petugas_id' => Auth::id(),
            'tanggal_inspeksi' => Carbon::parse($request->tanggal_inspeksi),
            'kondisi_kebersihan' => $request->kondisi_kebersihan,
            'ketersediaan_air' => $request->ketersediaan_air,
            'ketersediaan_sabun' => $request->ketersediaan_sabun,
            'bau_tidak_sedap' => $request->bau_tidak_sedap,
            'catatan' => $request->catatan,
            'foto' => $fotoPath,
            'status_tindak_lanjut' => $request->status_tindak_lanjut,
            'is_completed' => $isCompleted,
        ]);

        if (!$isCompleted) {
            Auth::user()->update(['status_pengerjaan' => 'aktif']);
        }

        $inspeksi->load(['facility', 'officer']);
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new InspectionAlertNotification($inspeksi));

        return redirect()->route('inspections.history')->with('success', 'Inspeksi berhasil dicatat.');
    }

    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            return abort(403, 'Unauthorized action.');
        }

        $inspection = Inspeksi::findOrFail($id);
        $facility = $inspection->facility;
        $petugasId = $inspection->petugas_id;
        $wasActive = !$inspection->is_completed;

        $oldFoto = $inspection->foto;
        $oldFotoSelesai = $inspection->foto_selesai;

        if ($facility) {
            $latest = Inspeksi::where('fasilitas_id', $facility->id)
                ->where('id', '!=', $inspection->id)
                ->orderBy('tanggal_inspeksi', 'desc')
                ->first();
            if ($latest) {
                if ($latest->is_completed) {
                    if ($latest->foto_selesai) {
                        $facility->update([
                            'foto_before' => $latest->foto,
                            'foto_after' => $latest->foto_selesai,
                        ]);
                    } else {
                        $facility->update([
                            'foto_before' => null,
                            'foto_after' => $latest->foto,
                        ]);
                    }
                } else {
                    $facility->update([
                        'foto_before' => $latest->foto,
                        'foto_after' => null,
                    ]);
                }
            } else {
                $facility->update([
                    'foto_before' => null,
                    'foto_after' => null,
                ]);
            }
        }

        $inspection->delete();

        if ($wasActive && $petugasId) {
            $hasActiveTasks = Inspeksi::where('petugas_id', $petugasId)
                ->where('is_completed', false)
                ->exists();
            if (!$hasActiveTasks) {
                User::where('id', $petugasId)->update(['status_pengerjaan' => 'ready']);
            }
        }

        if ($oldFoto) {
            $isUsed = Inspeksi::where('foto', $oldFoto)
                ->orWhere('foto_selesai', $oldFoto)
                ->exists();
            if (!$isUsed) {
                Storage::disk('public')->delete($oldFoto);
            }
        }
        if ($oldFotoSelesai) {
            $isUsed = Inspeksi::where('foto', $oldFotoSelesai)
                ->orWhere('foto_selesai', $oldFotoSelesai)
                ->exists();
            if (!$isUsed) {
                Storage::disk('public')->delete($oldFotoSelesai);
            }
        }

        return redirect()->route('inspections.history')->with('success', 'Inspeksi berhasil dihapus.');
    }

    public function destroyAll()
    {
        if (Auth::user()->role !== 'admin') {
            return abort(403, 'Unauthorized action.');
        }

        $inspections = Inspeksi::all();
        foreach ($inspections as $ins) {
            if ($ins->foto) {
                Storage::disk('public')->delete($ins->foto);
            }
            if ($ins->foto_selesai) {
                Storage::disk('public')->delete($ins->foto_selesai);
            }
        }

        // Delete all remaining files inside facilities directory (including facility's foto_before/foto_after)
        Storage::disk('public')->deleteDirectory('facilities');

        Inspeksi::query()->delete();

        // Reset all officers to ready
        User::where('role', 'petugas')->update(['status_pengerjaan' => 'ready']);

        // Reset all facilities' photos
        Fasilitas::query()->update([
            'foto_before' => null,
            'foto_after' => null,
        ]);

        return redirect()->route('inspections.history')->with('success', 'Seluruh riwayat inspeksi berhasil dihapus.');
    }
}
