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

        $query = Fasilitas::where('status_aktif', true);
        if (Auth::user()->role !== 'admin') {
            $query->where('penanggung_jawab', Auth::id());
        }
        $facilities = $query->get();
        return view('inspections.index', compact('facilities'));
    }

    public function history(Request $request)
    {
        $facilitiesQuery = Fasilitas::where('status_aktif', true);
        if (Auth::user()->role !== 'admin') {
            $facilitiesQuery->where('penanggung_jawab', Auth::id());
        }
        $facilities = $facilitiesQuery->get();

        $query = Inspeksi::with(['facility', 'officer']);

        if (Auth::user()->role !== 'admin') {
            // Petugas hanya bisa melihat riwayat inspeksi dari fasilitas miliknya
            $query->whereHas('facility', function($q) {
                $q->where('penanggung_jawab', Auth::id());
            });
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
        if ($facility->penanggung_jawab !== Auth::id()) {
            abort(403, 'Anda tidak bertanggung jawab atas fasilitas ini.');
        }

        if ($request->hasFile('foto_after')) {
            $facility = Fasilitas::findOrFail($request->fasilitas_id);

            if ($facility->foto_before) {
                Storage::disk('public')->delete($facility->foto_before);
            }
            if ($facility->foto_after) {
                Storage::disk('public')->delete($facility->foto_after);
            }

            $fotoAfterPath = $request->file('foto_after')->store('facilities', 'public');

            $facility->update([
                'foto_before' => null,
                'foto_after' => $fotoAfterPath,
            ]);
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
            'status_tindak_lanjut' => $request->status_tindak_lanjut,
        ]);

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
        $inspection->delete();

        return redirect()->route('inspections.history')->with('success', 'Inspeksi berhasil dihapus.');
    }
}
