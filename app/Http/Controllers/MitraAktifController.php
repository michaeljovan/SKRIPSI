<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekapKerjaSama;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MitraAktifController extends Controller
{
    public function index()
    {
        $mitraaktif = RekapKerjaSama::query()
            ->select(
                'mitra_kerja_sama',
                DB::raw('COUNT(CASE WHEN JSON_CONTAINS(bentuk_kerja_sama, \'"Implementasi"\') THEN 1 END) as total_implementasi'),
                DB::raw('COUNT(*) as total_kerjasama'),
                DB::raw('MAX(updated_at) as terakhir_aktif')
            )
            ->groupBy('mitra_kerja_sama')
            ->orderByDesc('total_implementasi') // Urutkan dari implementasi terbanyak
            ->get(); // Hapus take(5) untuk menampilkan semua

        return view('mitraaktif', [
            'mitraaktif' => $mitraaktif,
            'now' => Carbon::now()
        ]);
    }
}
