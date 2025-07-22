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
                DB::raw('COUNT(CASE WHEN jenis_kerja_sama = "IA" THEN 1 END) as total_implementasi'),
                DB::raw('COUNT(CASE WHEN bentuk_kerja_sama LIKE "%Implementasi%" THEN 1 END) as total_dengan_implementasi'),
                DB::raw('COUNT(*) as total_kerjasama')
            )
            ->groupBy('mitra_kerja_sama')
            ->orderByDesc('total_implementasi')
            ->orderByDesc('total_kerjasama')
            ->take(5)
            ->get();

        // Query untuk mitra tidak teraktif
        $mitrapasif = RekapKerjaSama::query()
            ->select(
                'mitra_kerja_sama',
                DB::raw('COUNT(CASE WHEN jenis_kerja_sama = "IA" THEN 1 END) as total_implementasi'),
                DB::raw('COUNT(CASE WHEN bentuk_kerja_sama LIKE "%Implementasi%" THEN 1 END) as total_dengan_implementasi'),
                DB::raw('COUNT(*) as total_kerjasama'),
                DB::raw('MAX(updated_at) as terakhir_aktif')
            )
            ->groupBy('mitra_kerja_sama')
            ->orderBy('total_implementasi', 'asc')
            ->orderBy('terakhir_aktif', 'asc')
            ->take(5)
            ->get();

        // Query untuk jenis kerja sama terbanyak dan tersedikit dari IA
        $jenisKerjaSamaStats = RekapKerjaSama::query()
            ->select(
                'jenis_kerja_sama',
                DB::raw('COUNT(*) as total')
            )
            ->where('jenis_kerja_sama', 'IA')
            ->groupBy('jenis_kerja_sama')
            ->orderBy('total', 'desc')
            ->get();

        $jenisTerbanyak = $jenisKerjaSamaStats->first();
        $jenisTersedikit = $jenisKerjaSamaStats->last();

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
                DB::raw('COUNT(CASE WHEN jenis_kerja_sama = "MoU" THEN 1 END) as total_mou'),
                DB::raw('COUNT(CASE WHEN jenis_kerja_sama = "MoA" THEN 1 END) as total_moa'),
                DB::raw('COUNT(CASE WHEN jenis_kerja_sama = "IA" THEN 1 END) as total_implementasi')
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

        $kategoriData = [];

        foreach ($orderedUnits as $unit) {
            $nasional = RekapKerjaSama::where('unit', $unit)->where('kategori', 'Nasional')->count();
            $internasional = RekapKerjaSama::where('unit', $unit)->where('kategori', 'Internasional')->count();

            $kategoriData[] = [
                'unit' => $unit,
                'nasional' => $nasional,
                'internasional' => $internasional,
            ];
        }

        $expiringAgreements = RekapKerjaSama::where('tanggal_selesai', '>=', now()) // Hanya yang belum kadaluarsa
            ->orderBy('tanggal_selesai', 'asc') // Urutkan dari yang paling dekat
            ->take(5) // Ambil 5 teratas
            ->get();



        $bentukData = RekapKerjaSama::select(
            'unit',
            DB::raw("SUM(CASE WHEN bentuk_kerja_sama LIKE '%Pendidikan%' THEN 1 ELSE 0 END) as pendidikan"),
            DB::raw("SUM(CASE WHEN bentuk_kerja_sama LIKE '%Penelitian%' THEN 1 ELSE 0 END) as penelitian"),
            DB::raw("SUM(CASE WHEN bentuk_kerja_sama LIKE '%Pengabdian%' THEN 1 ELSE 0 END) as pengabdian")
        )
            ->whereIn('unit', $orderedUnits)
            ->groupBy('unit')
            ->get();

        $bentukPerUnitChart = [];

        foreach ($orderedUnits as $unit) {
            $data = $bentukData->firstWhere('unit', $unit);
            $bentukPerUnitChart[] = [
                'unit' => $unit,
                'pendidikan' => $data ? (int)$data->pendidikan : 0,
                'penelitian' => $data ? (int)$data->penelitian : 0,
                'pengabdian' => $data ? (int)$data->pengabdian : 0,
            ];
        }

        return view('dashboard', compact(
            'mitraaktif',
            'mitrapasif',
            'chartData',
            'tahunList',
            'lineChartData',
            'jenisTerbanyak',
            'jenisTersedikit',
            'expiringAgreements',
            'kategoriData',
            'bentukPerUnitChart'
        ));
    }


    public function filterKategori(Request $request)
    {
        $year = $request->query('year');

        $data = RekapKerjaSama::selectRaw("
            unit,
            SUM(CASE WHEN kategori = 'Nasional' THEN 1 ELSE 0 END) AS nasional,
            SUM(CASE WHEN kategori = 'Internasional' THEN 1 ELSE 0 END) AS internasional
        ")
            ->when($year !== 'all', function ($query) use ($year) {
                $query->whereYear('tanggal_mulai', $year);
            })
            ->groupBy('unit')
            ->get();

        return response()->json($data);
    }


    public function filterByYear(Request $request)
    {
        $year = $request->query('year');

        $query = RekapKerjaSama::query()
            ->select(
                'unit',
                DB::raw('COUNT(CASE WHEN jenis_kerja_sama = "MoU" THEN 1 END) as total_mou'),
                DB::raw('COUNT(CASE WHEN jenis_kerja_sama = "MoA" THEN 1 END) as total_moa'),
                DB::raw('COUNT(CASE WHEN jenis_kerja_sama = "IA" THEN 1 END) as total_implementasi')
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
