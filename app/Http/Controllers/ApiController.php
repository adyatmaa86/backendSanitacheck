<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use App\Models\Inspeksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ApiController extends Controller
{
    /**
     * GET /api/user
     * Get authenticated user data
     */
    public function getUser(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ]);
    }

    /**
     * GET /api/fasilitas
     * Get all active facilities with cleanliness status
     */
    public function index()
    {
        $facilities = Fasilitas::with('petugas', 'latestInspection')->where('status_aktif', true)->get()->map(function ($f) {
            return [
                'id' => $f->id,
                'nama_fasilitas' => $f->nama_fasilitas,
                'jenis_fasilitas' => $f->jenis_fasilitas,
                'lokasi' => $f->lokasi,
                'penanggung_jawab' => $f->penanggung_jawab,
                'nama_petugas' => $f->petugas ? $f->petugas->name : 'Tidak ada',
                'no_telp_petugas' => $f->petugas ? $f->petugas->phone_number : '',
                'status_aktif' => $f->status_aktif,
                'foto_before' => $f->foto_before,
                'foto_after' => $f->foto_after,
                'cleanliness_status' => $f->cleanliness_status,
                'latest_inspection' => $f->latestInspection,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $facilities
        ], 200);
    }

    /**
     * GET /api/fasilitas/{id}/inspeksi
     * Get inspection history of a facility
     */
    public function inspectionHistory($id)
    {
        $facility = Fasilitas::with('petugas')->find($id);

        if (!$facility) {
            return response()->json([
                'status' => 'error',
                'message' => 'Facility not found'
            ], 404);
        }

        $inspections = $facility->inspections()
            ->with('officer:id,name,email')
            ->orderBy('tanggal_inspeksi', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'facility' => [
                'id' => $facility->id,
                'nama_fasilitas' => $facility->nama_fasilitas,
                'jenis_fasilitas' => $facility->jenis_fasilitas,
                'lokasi' => $facility->lokasi,
                'penanggung_jawab' => $facility->penanggung_jawab,
                'nama_petugas' => $facility->petugas ? $facility->petugas->name : 'Tidak ada',
                'no_telp_petugas' => $facility->petugas ? $facility->petugas->phone_number : '',
                'foto_before' => $facility->foto_before,
                'foto_after' => $facility->foto_after,
                'cleanliness_status' => $facility->cleanliness_status,
            ],
            'data' => $inspections
        ], 200);
    }

    /**
     * POST /api/inspeksi-sanitasi
     * Add new sanitation inspection
     */
    public function storeInspection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fasilitas_id' => 'required|exists:fasilitas,id',
            'tanggal_inspeksi' => 'required|date',
            'kondisi_kebersihan' => 'required|in:baik,cukup,buruk',
            'ketersediaan_air' => 'required|in:tersedia,tidak',
            'ketersediaan_sabun' => 'required|in:tersedia,tidak',
            'bau_tidak_sedap' => 'required|in:ya,tidak',
            'catatan' => 'nullable|string|max:1000',
            'status_tindak_lanjut' => 'required|in:aman,perlu dibersihkan,perlu perbaikan',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $tanggal = Carbon::parse($request->tanggal_inspeksi);

        $inspection = Inspeksi::create([
            'fasilitas_id' => $request->fasilitas_id,
            'petugas_id' => $request->user()->id,
            'tanggal_inspeksi' => $tanggal,
            'kondisi_kebersihan' => $request->kondisi_kebersihan,
            'ketersediaan_air' => $request->ketersediaan_air,
            'ketersediaan_sabun' => $request->ketersediaan_sabun,
            'bau_tidak_sedap' => $request->bau_tidak_sedap,
            'catatan' => $request->catatan,
            'status_tindak_lanjut' => $request->status_tindak_lanjut,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Inspection report created successfully',
            'data' => $inspection
        ], 201);
    }

    /**
     * GET /api/fasilitas/status/{status}
     * Get facilities filtered by cleanliness status
     */
    public function filterByStatus($status)
    {
        // Valid status values: bersih, perlu dibersihkan, buruk
        $validStatuses = ['bersih', 'perlu dibersihkan', 'buruk'];
        if (!in_array($status, $validStatuses)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid status filter. Choose: bersih, perlu dibersihkan, atau buruk'
            ], 400);
        }

        $facilities = Fasilitas::with('petugas', 'latestInspection')->where('status_aktif', true)->get()->map(function ($f) {
            return [
                'id' => $f->id,
                'nama_fasilitas' => $f->nama_fasilitas,
                'jenis_fasilitas' => $f->jenis_fasilitas,
                'lokasi' => $f->lokasi,
                'penanggung_jawab' => $f->penanggung_jawab,
                'nama_petugas' => $f->petugas ? $f->petugas->name : 'Tidak ada',
                'no_telp_petugas' => $f->petugas ? $f->petugas->phone_number : '',
                'status_aktif' => $f->status_aktif,
                'foto_before' => $f->foto_before,
                'foto_after' => $f->foto_after,
                'cleanliness_status' => $f->cleanliness_status,
                'latest_inspection' => $f->latestInspection,
            ];
        })->filter(function ($f) use ($status) {
            return $f['cleanliness_status'] === $status;
        })->values();

        return response()->json([
            'status' => 'success',
            'data' => $facilities
        ], 200);
    }
}
