<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekapKerjaSama;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Query untuk mitra teraktif
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

        // Query untuk mitra tidak teraktif
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

        // Query untuk chart unit kerja sama
        $unitData = RekapKerjaSama::query()
            ->select(
                'unit',
                DB::raw('COUNT(CASE WHEN JSON_CONTAINS(bentuk_kerja_sama, \'"MoU"\') THEN 1 END) as total_mou'),
                DB::raw('COUNT(CASE WHEN JSON_CONTAINS(bentuk_kerja_sama, \'"MoA"\') THEN 1 END) as total_moa'),
                DB::raw('COUNT(CASE WHEN JSON_CONTAINS(bentuk_kerja_sama, \'"Implementasi"\') THEN 1 END) as total_implementasi')
            )
            ->whereIn('unit', ['Fakultas Teknologi Informasi', 'Sistem Informasi', 'Informatika']) // Perubahan di sini
            ->groupBy('unit')
            ->get();

        // Format data untuk chart
        $chartData = [];
        foreach ($unitData as $unit) {
            $chartData[] = [
                'unit' => $unit->unit,
                'mou' => $unit->total_mou,
                'moa' => $unit->total_moa,
                'implementasi' => $unit->total_implementasi
            ];
        }

        // Tambahkan data kosong jika kurang dari 5
        $mitraaktif = $this->padWithEmptyData($mitraaktif, 5);
        $mitrapasif = $this->padWithEmptyData($mitrapasif, 5);

        return view('dashboard', compact('mitraaktif', 'mitrapasif', 'chartData'));
    }

    private function padWithEmptyData($collection, $count)
    {
        $emptyItemsNeeded = $count - $collection->count();

        if ($emptyItemsNeeded > 0) {
            $emptyData = array_fill(0, $emptyItemsNeeded, (object)[
                'mitra_kerja_sama' => '-',
                'total_implementasi' => '-',
                'total_kerjasama' => '-'
            ]);

            return $collection->concat($emptyData);
        }

        return $collection;
    }
}
