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

        if (Auth::user()->role !== 'admin') {
            $query->where(function ($q) {
                $q->where('penanggung_jawab', Auth::id())
                  ->orWhereHas('petugasTambahan', function ($q2) {
                      $q2->where('user_id', Auth::id());
                  });
            });
        }

        if ($search) {
            $query->where('nama_fasilitas', 'like', "%{$search}%");
        }

        if ($jenis) {
            $query->where('jenis_fasilitas', $jenis);
        }

        $facilities = $query->orderBy('nama_fasilitas', 'asc')->paginate(5);

        $metricsQuery = Fasilitas::query();
        if (Auth::user()->role !== 'admin') {
            $metricsQuery->where(function ($q) {
                $q->where('penanggung_jawab', Auth::id())
                  ->orWhereHas('petugasTambahan', function ($q2) {
                      $q2->where('user_id', Auth::id());
                  });
            });
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

        if (Auth::user()->role === 'admin') {
            $listJenis = \App\Models\JenisFasilitas::all();
        } else {
            $ownedSlugs = Fasilitas::where(function ($q) {
                    $q->where('penanggung_jawab', Auth::id())
                      ->orWhereHas('petugasTambahan', function ($q2) {
                          $q2->where('user_id', Auth::id());
                      });
                })
                ->whereNotNull('jenis_fasilitas')
                ->distinct()
                ->pluck('jenis_fasilitas');
            $listJenis = \App\Models\JenisFasilitas::whereIn('slug', $ownedSlugs)->get();
        }
        $listPetugas = User::where('role', 'petugas')->get();

        if ($request->ajax()) {
            return view('facilities.partials.table', compact('facilities'))->render();
        }

        return view('facilities.index', compact(
            'facilities', 
            'search',
            'jenis',
            'totalFacilities',
            'compliantCount',
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
            'petugas_tambahan_ids' => 'nullable|array',
            'petugas_tambahan_ids.*' => 'exists:users,id',
            'foto_before' => 'required|image|mimes:jpeg,png,jpg|max:2048',
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

        if ($request->has('_clear_tambahan')) {
            $facility->petugasTambahan()->sync([]);
        } elseif ($request->has('petugas_tambahan_ids')) {
            $facility->petugasTambahan()->sync($request->petugas_tambahan_ids);
        }

        $tambahanIds = $request->has('_clear_tambahan') ? [] : ($request->petugas_tambahan_ids ?? []);
        $allOfficerIds = collect([$request->petugas_id])
            ->merge($tambahanIds)
            ->unique();
        $officers = User::whereIn('id', $allOfficerIds)->get();
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
            'petugas_tambahan_ids' => 'nullable|array',
            'petugas_tambahan_ids.*' => 'exists:users,id',
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

        $oldPetugasId = $facility->penanggung_jawab;
        $newPetugasId = (int)$request->petugas_id;
        $oldTambahanIds = $facility->petugasTambahan->pluck('id')->toArray();

        $facility->update($data);

        if ($request->has('_clear_tambahan')) {
            $facility->petugasTambahan()->sync([]);
            $newTambahanIds = [];
        } elseif ($request->has('petugas_tambahan_ids')) {
            $facility->petugasTambahan()->sync($request->petugas_tambahan_ids);
            $newTambahanIds = array_map('intval', $request->petugas_tambahan_ids);
        } else {
            $newTambahanIds = [];
        }

        $removedPetugasIds = array_diff($oldTambahanIds, $newTambahanIds);
        if ($oldPetugasId !== $newPetugasId) {
            $removedPetugasIds[] = $oldPetugasId;
            
            $activeInspection = \App\Models\Inspeksi::where('fasilitas_id', $facility->id)
                ->where('is_completed', false)
                ->first();

            if ($activeInspection) {
                $activeInspection->update(['petugas_id' => $newPetugasId]);
                User::where('id', $newPetugasId)->update(['status_pengerjaan' => 'aktif']);
            }
        }

        foreach ($removedPetugasIds as $pId) {
            $hasActive = \App\Models\Inspeksi::where('petugas_id', $pId)
                ->where('is_completed', false)
                ->exists();
            if (!$hasActive) {
                User::where('id', $pId)->update(['status_pengerjaan' => 'ready']);
            }
        }

        $tambahanIds = $request->has('_clear_tambahan') ? [] : ($request->petugas_tambahan_ids ?? []);
        $allOfficerIds = collect([$request->petugas_id])
            ->merge($tambahanIds)
            ->unique();
        $officers = User::whereIn('id', $allOfficerIds)->get();
        Notification::send($officers, new FacilityAlertNotification($facility, 'diperbarui'));

        return redirect()->route('facilities.index')->with('success', 'Fasilitas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            return abort(403, 'Unauthorized action.');
        }

        $facility = Fasilitas::findOrFail($id);

        $activeInspections = \App\Models\Inspeksi::where('fasilitas_id', $facility->id)
            ->where('is_completed', false)
            ->get();

        $officerIds = $activeInspections->pluck('petugas_id')->filter()->unique();

        $facility->delete();

        foreach ($officerIds as $officerId) {
            $hasActiveTasks = \App\Models\Inspeksi::where('petugas_id', $officerId)
                ->where('is_completed', false)
                ->exists();
            if (!$hasActiveTasks) {
                User::where('id', $officerId)->update(['status_pengerjaan' => 'ready']);
            }
        }

        return redirect()->route('facilities.index')->with('success', 'Fasilitas berhasil dihapus.');
    }
}
