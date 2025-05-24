<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekapKerjaSama;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MitraPasifController extends Controller
{
    public function index(Request $request)
    {
        $mitrapasif = RekapKerjaSama::query()
            ->select([
                'mitra_kerja_sama',
                DB::raw('COUNT(CASE WHEN jenis_kerja_sama = "MoU" THEN 1 END) as jumlah_mou'),
                DB::raw('COUNT(CASE WHEN jenis_kerja_sama = "MoA" THEN 1 END) as jumlah_moa'),
                DB::raw('COUNT(CASE WHEN jenis_kerja_sama = "IA" THEN 1 END) as total_implementasi'),
                DB::raw('COUNT(*) as total_kerjasama'),
                DB::raw('MAX(updated_at) as terakhir_aktif')
            ])
            ->groupBy('mitra_kerja_sama')
            ->orderBy('total_implementasi', 'asc')
            ->orderBy('terakhir_aktif', 'asc')
            ->get();

        return view('mitrapasif', [
            'mitrapasif' => $mitrapasif,
            'now' => Carbon::now()
        ]);
    }
}
