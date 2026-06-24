<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use App\Models\User;
use App\Notifications\FacilityAlertNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class FasilitasController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $jenis = $request->input('jenis_fasilitas');
        $query = Fasilitas::query()->with('petugas', 'latestInspection');

        // Jika petugas login, filter fasilitas hanya miliknya (berdasarkan email/username petugas yang login)
        // User login menggunakan Auth::user(). Mari kita gunakan id petugas atau name/email, request berkata "dengan username yang dimiliki oleh petugas yang login"
        // di laravel, user memiliki email (yang sering dianggap username login) atau id. Kita filter berdasarkan petugas_id matching Auth::id() jika role bukan admin.
        if (Auth::user()->role !== 'admin') {
            $query->where('penanggung_jawab', Auth::id());
        }

        if ($search) {
            $query->where('nama_fasilitas', 'like', "%{$search}%");
        }

        if ($jenis) {
            $query->where('jenis_fasilitas', $jenis);
        }

        $facilities = $query->orderBy('nama_fasilitas', 'desc')->paginate(5);

        // Calculate metrics for summary cards
        $metricsQuery = Fasilitas::query();
        if (Auth::user()->role !== 'admin') {
            $metricsQuery->where('penanggung_jawab', Auth::id());
        }
        $allFacilities = $metricsQuery->with('latestInspection')->get();
        $totalFacilities = $allFacilities->count();
        
        $criticalCount = 0;
        $pendingCount = 0;
        $compliantCount = 0;

        foreach ($allFacilities as $facility) {
            $status = $facility->cleanliness_status;
            if ($status === 'bersih') {
                $compliantCount++;
            } elseif ($status === 'buruk') {
                $criticalCount++;
            } elseif ($status === 'perlu dibersihkan') {
                $pendingCount++;
            }
        }

        $listJenis = \App\Models\JenisFasilitas::all();
        $listPetugas = User::where('role', 'petugas')->get();

        if ($request->ajax()) {
            return view('facilities.partials.table', compact('facilities'))->render();
        }

        return view('facilities.index', compact(
            'facilities', 
            'search',
            'jenis',
            'totalFacilities',
            'criticalCount',
            'pendingCount',
            'listJenis',
            'listPetugas'
        ));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'nama_fasilitas' => 'required|string|max:255',
            'jenis_fasilitas' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'petugas_id' => 'required|exists:users,id',
            'foto_before' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status_aktif' => 'sometimes|boolean',
        ]);

        $fotoBeforePath = null;
        if ($request->hasFile('foto_before')) {
            $fotoBeforePath = $request->file('foto_before')->store('facilities', 'public');
        }

        $facility = Fasilitas::create([
            'nama_fasilitas' => $request->nama_fasilitas,
            'jenis_fasilitas' => $request->jenis_fasilitas,
            'lokasi' => $request->lokasi,
            'penanggung_jawab' => $request->petugas_id,
            'status_aktif' => $request->has('status_aktif'),
            'foto_before' => $fotoBeforePath,
        ]);

        $officers = User::where('role', 'petugas')->get();
        Notification::send($officers, new FacilityAlertNotification($facility, 'ditambahkan'));

        return redirect()->route('facilities.index')->with('success', 'Fasilitas berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            return abort(403, 'Unauthorized action.');
        }

        $facility = Fasilitas::findOrFail($id);

        $request->validate([
            'nama_fasilitas' => 'required|string|max:255',
            'jenis_fasilitas' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'petugas_id' => 'required|exists:users,id',
            'foto_before' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status_aktif' => 'sometimes|boolean',
        ]);

        $data = [
            'nama_fasilitas' => $request->nama_fasilitas,
            'jenis_fasilitas' => $request->jenis_fasilitas,
            'lokasi' => $request->lokasi,
            'penanggung_jawab' => $request->petugas_id,
            'status_aktif' => $request->has('status_aktif'),
        ];

        if ($request->hasFile('foto_before')) {
            if ($facility->foto_before) {
                Storage::disk('public')->delete($facility->foto_before);
            }
            if ($facility->foto_after) {
                Storage::disk('public')->delete($facility->foto_after);
            }
            $data['foto_before'] = $request->file('foto_before')->store('facilities', 'public');
            $data['foto_after'] = null;
        }

        $facility->update($data);

        $officers = User::where('role', 'petugas')->get();
        Notification::send($officers, new FacilityAlertNotification($facility, 'diperbarui'));

        return redirect()->route('facilities.index')->with('success', 'Fasilitas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            return abort(403, 'Unauthorized action.');
        }

        $facility = Fasilitas::findOrFail($id);
        $facility->delete();

        return redirect()->route('facilities.index')->with('success', 'Fasilitas berhasil dihapus.');
    }
}
