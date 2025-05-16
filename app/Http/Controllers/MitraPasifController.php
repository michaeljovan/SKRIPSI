<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekapKerjaSama;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MitraPasifController extends Controller
{
    public function index()
    {
        $mitrapasif = RekapKerjaSama::query()
            ->select(
                'mitra_kerja_sama',
                DB::raw('COUNT(CASE WHEN JSON_CONTAINS(bentuk_kerja_sama, \'"Implementasi"\') THEN 1 END) as total_implementasi'),
                DB::raw('COUNT(*) as total_kerjasama'),
                DB::raw('MAX(updated_at) as terakhir_aktif')
            )
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
