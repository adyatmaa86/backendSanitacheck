<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use App\Models\Inspeksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $facilities = Fasilitas::where('status_aktif', true)
            ->with('latestInspection')
            ->when(Auth::user()->role !== 'admin', function ($q) {
                $q->where(function ($q2) {
                    $q2->where('penanggung_jawab', Auth::id())
                       ->orWhereHas('petugasTambahan', function ($q3) {
                           $q3->where('user_id', Auth::id());
                       });
                });
            })
            ->get();
        $totalFacilities = $facilities->count();
        $needFollowUp = 0;
        $compliantCount = 0;
        $criticalCount = 0;
        $pendingCount = 0;
        $uninspectedCount = 0;

        foreach ($facilities as $facility) {
            $status = $facility->cleanliness_status;
            if ($status === 'bersih') {
                $compliantCount++;
            } elseif ($status === 'buruk') {
                $criticalCount++;
                $needFollowUp++;
            } elseif ($status === 'perlu dibersihkan') {
                $pendingCount++;
            } elseif ($status === 'belum_inspeksi') {
                $uninspectedCount++;
            }
        }

        $complianceScore = $totalFacilities > 0 ? round(($compliantCount / $totalFacilities) * 100, 1) : 100;

        // Calculate 7 days inspection trends
        $inspectionTrends = [];
        $dayNamesIndo = [
            'Sun' => 'Min',
            'Mon' => 'Sen',
            'Tue' => 'Sel',
            'Wed' => 'Rab',
            'Thu' => 'Kam',
            'Fri' => 'Jum',
            'Sat' => 'Sab',
        ];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $engDay = $date->format('D');
            $label = $dayNamesIndo[$engDay] ?? $engDay;
            
            $count = Inspeksi::whereDate('tanggal_inspeksi', $date)
                ->when(Auth::user()->role !== 'admin', function ($q) {
                    $q->where('petugas_id', Auth::id());
                })
                ->count();
            
            $inspectionTrends[] = [
                'label' => $label,
                'count' => $count,
                'is_today' => $i === 0
            ];
        }
        
        $maxInspectionCount = collect($inspectionTrends)->max('count');
        // Gunakan jumlah fasilitas atau minimal 10 sebagai batas atas default agar skala chart lebih luas dan tidak kaku
        $maxInspectionCount = max($maxInspectionCount, $totalFacilities, 10);
        
        foreach ($inspectionTrends as &$trend) {
            $trend['percentage'] = round(($trend['count'] / $maxInspectionCount) * 100);
        }

        $recentInspections = Inspeksi::with(['facility', 'officer'])
            ->when(Auth::user()->role !== 'admin', function ($q) {
                $q->where('petugas_id', Auth::id());
            })
            ->orderBy('tanggal_inspeksi', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'totalFacilities',
            'needFollowUp',
            'complianceScore',
            'recentInspections',
            'compliantCount',
            'criticalCount',
            'pendingCount',
            'uninspectedCount',
            'inspectionTrends',
            'maxInspectionCount'
        ));
    }
}
