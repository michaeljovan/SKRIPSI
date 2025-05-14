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
        $rekap = RekapKerjaSama::findOrFail($id);
        return view('inputlaporanpelaksanaankerjasama', compact('rekap'));
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

    public function edit($id)
    {
        $pelaksanaan = PelaksanaanKerjaSama::with('rekap')->findOrFail($id);

        if (!$pelaksanaan->rekap) {
            abort(404, 'Associated rekap record not found');
        }

        return view('editpelaksanaankerjasama', [
            'pelaksanaan' => $pelaksanaan,
            'rekap' => $pelaksanaan->rekap
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'ruang_lingkup' => 'required',
            'dosen_terlibat' => 'nullable',
            'mahasiswa_terlibat' => 'nullable',
            'anggaran_ukdw' => 'required|numeric',
            'hasil_pelaksanaan' => 'required',
            'tautan_link_kegiatan' => 'nullable|url'
        ]);

        // Format angka (remove dots if using thousand separators)
        if ($request->anggaran_ukdw) {
            $validated['anggaran_ukdw'] = str_replace('.', '', $request->anggaran_ukdw);
        }

        $pelaksanaan = PelaksanaanKerjaSama::findOrFail($id);
        $pelaksanaan->update($validated);


        return redirect()->route('pelaksanaankerjasama.index')
            ->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy($id)
    {
        try {
            $pelaksanaan = PelaksanaanKerjaSama::findOrFail($id);

            // Update the associated RekapKerjaSama
            RekapKerjaSama::where('id', $pelaksanaan->idrekap)
                ->update(['is_laporan' => false]);

            // Delete the pelaksanaan record
            $pelaksanaan->delete();

            return redirect()->route('pelaksanaankerjasama.index')
                ->with('success', 'Laporan pelaksanaan berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
