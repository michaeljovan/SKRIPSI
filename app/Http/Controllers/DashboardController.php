<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekapKerjaSama;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Query untuk mitra teraktif dan tidak teraktif (tetap sama)
        $mitraaktif = RekapKerjaSama::query()
            ->select(
                'mitra_kerja_sama',
                DB::raw('COUNT(CASE WHEN JSON_CONTAINS(bentuk_kerja_sama, \'"Implementasi"\') THEN 1 END) as total_implementasi'),
                DB::raw('COUNT(*) as total_kerjasama')
            )
            ->groupBy('mitra_kerja_sama')
            ->orderByDesc('total_implementasi')
            ->take(5)
            ->get();

        $mitrapasif = RekapKerjaSama::query()
            ->select(
                'mitra_kerja_sama',
                DB::raw('COUNT(CASE WHEN JSON_CONTAINS(bentuk_kerja_sama, \'"Implementasi"\') THEN 1 END) as total_implementasi'),
                DB::raw('COUNT(*) as total_kerjasama')
            )
            ->groupBy('mitra_kerja_sama')
            ->having('total_implementasi', '>', 0)
            ->orderBy('total_implementasi')
            ->take(5)
            ->get();

        // Definisikan urutan unit yang diinginkan
        $orderedUnits = [
            'Fakultas Teknologi Informasi',
            'Informatika',
            'Sistem Informasi'
        ];

        // Query untuk chart column (distribusi per unit)
        $unitData = RekapKerjaSama::query()
            ->select(
                'unit',
                DB::raw('COUNT(CASE WHEN JSON_CONTAINS(bentuk_kerja_sama, \'"MoU"\') THEN 1 END) as total_mou'),
                DB::raw('COUNT(CASE WHEN JSON_CONTAINS(bentuk_kerja_sama, \'"MoA"\') THEN 1 END) as total_moa'),
                DB::raw('COUNT(CASE WHEN JSON_CONTAINS(bentuk_kerja_sama, \'"Implementasi"\') THEN 1 END) as total_implementasi')
            )
            ->whereIn('unit', $orderedUnits)
            ->groupBy('unit')
            ->get();

        // Format data untuk chart column dengan urutan yang benar
        $chartData = [];
        foreach ($orderedUnits as $unitName) {
            $unit = $unitData->firstWhere('unit', $unitName);
            $chartData[] = [
                'unit' => $unitName,
                'mou' => $unit ? $unit->total_mou : 0,
                'moa' => $unit ? $unit->total_moa : 0,
                'implementasi' => $unit ? $unit->total_implementasi : 0
            ];
        }

        // Query untuk tahun
        $tahunList = RekapKerjaSama::select(DB::raw('YEAR(tanggal_mulai) as tahun'))
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        // Query untuk chart line (5 tahun terakhir)
        $fiveYearsAgo = now()->subYears(4)->year;
        $unitKerjaPerTahun = RekapKerjaSama::select(
            'unit',
            DB::raw('YEAR(tanggal_mulai) as tahun'),
            DB::raw('COUNT(*) as total_kerjasama')
        )
            ->whereYear('tanggal_mulai', '>=', $fiveYearsAgo)
            ->whereIn('unit', $orderedUnits)
            ->groupBy('unit', DB::raw('YEAR(tanggal_mulai)'))
            ->orderBy('tahun')
            ->get();

        // Format data untuk chart line dengan urutan yang benar
        $lineChartData = [];
        foreach ($orderedUnits as $unitName) {
            $unitYears = $unitKerjaPerTahun->where('unit', $unitName);

            $yearData = [];
            for ($year = $fiveYearsAgo; $year <= now()->year; $year++) {
                $yearRecord = $unitYears->firstWhere('tahun', $year);
                $yearData[] = [
                    'label' => (string)$year,
                    'y' => $yearRecord ? $yearRecord->total_kerjasama : 0
                ];
            }

            $lineChartData[$unitName] = $yearData;
        }

        return view('dashboard', compact('mitraaktif', 'mitrapasif', 'chartData', 'tahunList', 'lineChartData'));
    }

    public function filterByYear(Request $request)
    {
        $year = $request->query('year');

        $query = RekapKerjaSama::query()
            ->select(
                'unit',
                DB::raw('COUNT(CASE WHEN JSON_CONTAINS(bentuk_kerja_sama, \'"MoU"\') THEN 1 END) as total_mou'),
                DB::raw('COUNT(CASE WHEN JSON_CONTAINS(bentuk_kerja_sama, \'"MoA"\') THEN 1 END) as total_moa'),
                DB::raw('COUNT(CASE WHEN JSON_CONTAINS(bentuk_kerja_sama, \'"Implementasi"\') THEN 1 END) as total_implementasi')
            )
            ->whereIn('unit', ['Fakultas Teknologi Informasi', 'Informatika', 'Sistem Informasi']);

        if ($year !== 'all') {
            $query->whereYear('tanggal_mulai', $year);
        }

        $unitData = $query->groupBy('unit')->get();

        // Urutkan secara manual
        $orderedUnits = [
            'Fakultas Teknologi Informasi',
            'Informatika',
            'Sistem Informasi'
        ];

        $chartData = [];
        foreach ($orderedUnits as $unitName) {
            $unit = $unitData->firstWhere('unit', $unitName);
            if ($unit) {
                $chartData[] = [
                    'unit' => $unit->unit,
                    'mou' => $unit->total_mou,
                    'moa' => $unit->total_moa,
                    'implementasi' => $unit->total_implementasi
                ];
            }
        }

        return response()->json($chartData);
    }
}
