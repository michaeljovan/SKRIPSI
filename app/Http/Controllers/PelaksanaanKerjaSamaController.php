<?php

namespace App\Http\Controllers;

use App\Models\PelaksanaanKerjaSama;
use App\Models\RekapKerjaSama;
use Illuminate\Http\Request;

class PelaksanaanKerjaSamaController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $kerjaSama = RekapKerjaSama::findOrFail($id);
        return view('pelaksanaan.create', compact('kerjaSama'));
    }

    public function index()
    {
        $rekap = RekapKerjaSama::with('laporanPelaksanaan')
                    ->whereHas('laporanPelaksanaan')
                    ->paginate(10);

        return view('laporanpelaksanaankerjasama', compact('rekap'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'rekap_id' => 'required|exists:rekapkerjasama,id',
                'ruang_lingkup' => 'required|string',
                'dosen_terlibat' => 'required|string',
                'mahasiswa_terlibat' => 'required|string',
                'anggaran_ukdw' => 'required|numeric',
                'hasil_pelaksanaan' => 'required|string',
                'tautan_kegiatan' => 'nullable|url',
            ]);

            // Clean currency values
            $validatedData['anggaran_ukdw'] = str_replace('.', '', $validatedData['anggaran_ukdw']);

            PelaksanaanKerjaSama::create([
                'idrekap' => $validatedData['rekap_id'],
                'ruang_lingkup' => $validatedData['ruang_lingkup'],
                'dosen_terlibat' => $validatedData['dosen_terlibat'],
                'mahasiswa_terlibat' => $validatedData['mahasiswa_terlibat'],
                'anggaran_ukdw' => $validatedData['anggaran_ukdw'],
                'hasil_pelaksanaan' => $validatedData['hasil_pelaksanaan'],
                'tautan_link_kegiatan' => $validatedData['tautan_kegiatan'],
            ]);

            RekapKerjaSama::findOrFail($validatedData['rekap_id'])->update([
                'is_laporan' => true,
            ]);

            return redirect()->back()->with('success', 'Laporan pelaksanaan berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
