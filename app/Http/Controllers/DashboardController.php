<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use App\Models\Inspeksi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $facilities = Fasilitas::where('status_aktif', true)->with('latestInspection')->get();
        $totalFacilities = $facilities->count();
        $inspectionsToday = Inspeksi::whereDate('tanggal_inspeksi', Carbon::today())
            ->pluck('fasilitas_id')
            ->unique()
            ->count();

        $needFollowUp = 0;
        $compliantCount = 0;
        $criticalCount = 0;
        $pendingCount = 0;

        foreach ($facilities as $facility) {
            $status = $facility->cleanliness_status;
            if ($status === 'bersih') {
                $compliantCount++;
            } elseif ($status === 'buruk') {
                $criticalCount++;
                $needFollowUp++;
            } elseif ($status === 'perlu dibersihkan') {
                $pendingCount++;
                $needFollowUp++;
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
            
            $count = Inspeksi::whereDate('tanggal_inspeksi', $date)->count();
            
            $inspectionTrends[] = [
                'label' => $label,
                'count' => $count,
                'is_today' => $i === 0
            ];
        }
        
        $maxInspectionCount = collect($inspectionTrends)->max('count');
        if ($maxInspectionCount <= 0) {
            $maxInspectionCount = 1; // avoid division by zero
        }
        
        foreach ($inspectionTrends as &$trend) {
            $trend['percentage'] = round(($trend['count'] / $maxInspectionCount) * 100);
        }

        $recentInspections = Inspeksi::with(['facility', 'officer'])
            ->orderBy('tanggal_inspeksi', 'desc')
            ->take(5)
            ->get();

        $todayInspectionRate = $totalFacilities > 0 ? min(round(($inspectionsToday / $totalFacilities) * 100), 100) : 0;

        return view('dashboard.index', compact(
            'totalFacilities',
            'inspectionsToday',
            'needFollowUp',
            'complianceScore',
            'recentInspections',
            'compliantCount',
            'criticalCount',
            'pendingCount',
            'inspectionTrends',
            'maxInspectionCount',
            'todayInspectionRate'
        ));
    }
}
