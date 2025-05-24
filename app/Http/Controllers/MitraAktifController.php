<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekapKerjaSama;
use Illuminate\Support\Facades\DB;

class MitraAktifController extends Controller
{
    public function index(Request $request)
    {
        $mitraAktif = RekapKerjaSama::query()
            ->select([
                'mitra_kerja_sama',
                DB::raw('COUNT(CASE WHEN jenis_kerja_sama = "MoU" THEN 1 END) as jumlah_mou'),
                DB::raw('COUNT(CASE WHEN jenis_kerja_sama = "MoA" THEN 1 END) as jumlah_moa'),
                DB::raw('COUNT(CASE WHEN jenis_kerja_sama = "IA" THEN 1 END) as total_implementasi'),
                DB::raw('COUNT(*) as total_kerjasama')
            ])
            ->groupBy('mitra_kerja_sama')
            ->orderByDesc('total_implementasi')
            ->orderByDesc('total_kerjasama')
            ->get();

        return view('mitraaktif', compact('mitraAktif'));
    }
}
