<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Fasilitas;
use App\Models\Inspeksi;
use App\Models\User;
use App\Notifications\LaporanAssignedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat mengakses halaman ini.');
        }

        $query = Laporan::with('facility', 'petugas');

        if ($request->filled('fasilitas_id')) {
            $query->where('fasilitas_id', $request->fasilitas_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $laporans = $query->orderBy('created_at', 'desc')->paginate(10);
        $facilities = Fasilitas::where('status_aktif', true)->with('petugas', 'petugasTambahan')->get();

        // Build facility-to-petugas mapping for the modal dropdown
        $facilityPetugasMap = [];
        foreach ($facilities as $fac) {
            $allPetugas = collect();
            if ($fac->petugas) {
                $allPetugas->push($fac->petugas);
            }
            foreach ($fac->petugasTambahan as $pt) {
                $allPetugas->push($pt);
            }
            $facilityPetugasMap[$fac->id] = $allPetugas->unique('id')->values()->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'status' => $p->status_pengerjaan,
            ]);
        }

        // Petugas yang sedang memproses laporan lain (pending/diproses)
        $busyPetugasIds = Laporan::whereIn('status', ['pending', 'diproses'])
            ->whereNotNull('petugas_id')
            ->pluck('petugas_id')
            ->unique()
            ->values()
            ->toArray();

        if ($request->ajax()) {
            return view('laporan.partials.table', compact('laporans'))->render();
        }

        return view('laporan.index', compact('laporans', 'facilities', 'facilityPetugasMap', 'busyPetugasIds'));
    }

    public function show($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $laporan = Laporan::with('facility', 'petugas')->findOrFail($id);
        return response()->json($laporan);
    }

    public function kirimKePetugas(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'petugas_id' => 'required|exists:users,id',
        ]);

        $laporan = Laporan::findOrFail($id);

        $facility = Fasilitas::with('petugas', 'petugasTambahan')->findOrFail($laporan->fasilitas_id);
        $allPetugasIds = collect([$facility->penanggung_jawab])
            ->merge($facility->petugasTambahan->pluck('id'))
            ->filter()
            ->unique()
            ->values();

        if (!$allPetugasIds->contains((int)$request->petugas_id)) {
            return redirect()->route('laporan.index')->with('error', 'Petugas tidak bertanggung jawab atas fasilitas ini.');
        }

        $petugas = User::findOrFail($request->petugas_id);
        $laporan->update([
            'petugas_id' => $petugas->id,
            'status' => 'diproses',
        ]);

        Notification::send($petugas, new LaporanAssignedNotification($laporan));

        return redirect()->route('laporan.index')->with('success', "Laporan berhasil dikirim ke {$petugas->name}.");
    }

    public function petugasLaporan()
    {
        if (Auth::user()->role !== 'petugas') {
            abort(403);
        }

        $laporans = Laporan::with('facility')
            ->where('petugas_id', Auth::id())
            ->where('status', 'diproses')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('petugas.terima-laporan', compact('laporans'));
    }

    public function terimaLaporan($id)
    {
        if (Auth::user()->role !== 'petugas') {
            abort(403);
        }

        $laporan = Laporan::where('id', $id)
            ->where('petugas_id', Auth::id())
            ->where('status', 'diproses')
            ->firstOrFail();

        $laporan->update(['status' => 'selesai']);

        $inspeksi = Inspeksi::create([
            'fasilitas_id' => $laporan->fasilitas_id,
            'petugas_id' => Auth::id(),
            'laporan_id' => $laporan->id,
            'tanggal_inspeksi' => now(),
            'kondisi_kebersihan' => 'cukup',
            'ketersediaan_air' => 'tidak',
            'ketersediaan_sabun' => 'tidak',
            'bau_tidak_sedap' => 'ya',
            'catatan' => "Dari laporan {$laporan->nama_pelapor}: {$laporan->keluhan}",
            'status_tindak_lanjut' => 'perlu dibersihkan',
            'is_completed' => false,
        ]);

        Auth::user()->update(['status_pengerjaan' => 'aktif']);

        return redirect()->route('petugas.tugas-saya')
            ->with('success', 'Laporan diterima. Silakan selesaikan tugas ini.');
    }

    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $laporan = Laporan::findOrFail($id);
        if ($laporan->foto_bukti) {
            Storage::disk('public')->delete($laporan->foto_bukti);
        }
        $laporan->delete();

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil dihapus.');
    }
}
