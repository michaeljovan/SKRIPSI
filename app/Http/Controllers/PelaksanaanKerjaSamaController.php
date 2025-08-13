<?php

namespace App\Http\Controllers;

use App\Models\PelaksanaanKerjaSama;
use App\Models\RekapKerjaSama;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class PelaksanaanKerjaSamaController extends Controller
{

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

                // Kolom baru untuk jumlah
                'jumlah_dosen_terlibat' => 'nullable|integer|min:0',
                'jumlah_mahasiswa_terlibat' => 'nullable|integer|min:0',

                // Nama-nama dosen & mahasiswa
                'dosen_terlibat' => 'nullable|string',
                'mahasiswa_terlibat' => 'nullable|string',

                'anggaran_ukdw' => 'required|numeric',
                'hasil_pelaksanaan' => 'required|string',
                'tautan_link_kegiatan' => 'nullable|url',
                'dokumen_kegiatan' => 'nullable|file|mimes:pdf|max:5120', // max 5MB
            ], [
                'anggaran_ukdw.numeric' => 'Anggaran UKDW harus berupa angka',
                'tautan_link_kegiatan.url' => 'Tautan kegiatan harus berupa URL yang valid',
                'dokumen_kegiatan.mimes' => 'Dokumen kegiatan harus berupa file PDF',
                'dokumen_kegiatan.max' => 'Ukuran file maksimal 5MB',
            ]);

            // Hilangkan titik dari anggaran (jika ada)
            $validatedData['anggaran_ukdw'] = str_replace('.', '', $validatedData['anggaran_ukdw']);

            // Simpan file PDF jika ada
            $filePath = null;
            if ($request->hasFile('dokumen_kegiatan')) {
                $file = $request->file('dokumen_kegiatan');
                $filePath = $file->store('dokumen_kegiatan', 'public');
            }

            // Simpan ke database
            PelaksanaanKerjaSama::create([
                'idrekap' => $validatedData['rekap_id'],
                'ruang_lingkup' => $validatedData['ruang_lingkup'],

                // Kolom jumlah
                'jumlah_dosen_terlibat' => $validatedData['jumlah_dosen_terlibat'] ?? 0,
                'jumlah_mahasiswa_terlibat' => $validatedData['jumlah_mahasiswa_terlibat'] ?? 0,

                // Kolom nama
                'dosen_terlibat' => $validatedData['dosen_terlibat'] ?? '',
                'mahasiswa_terlibat' => $validatedData['mahasiswa_terlibat'] ?? '',

                'anggaran_ukdw' => $validatedData['anggaran_ukdw'],
                'tautan_link_kegiatan' => $validatedData['tautan_link_kegiatan'] ?? '',
                'hasil_pelaksanaan' => $validatedData['hasil_pelaksanaan'],
                'dokumen_kegiatan' => $filePath,
            ]);

            // Update status laporan
            RekapKerjaSama::findOrFail($validatedData['rekap_id'])->update([
                'is_laporan' => true,
            ]);

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Laporan pelaksanaan berhasil disimpan'], 200);
            }

            return redirect()->route('pelaksanaankerjasama.index')
                ->with('success', 'Laporan pelaksanaan berhasil disimpan');
        } catch (ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $e->errors()], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.')->withInput();
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
            'jumlah_dosen_terlibat' => 'nullable|integer|min:0',
            'jumlah_mahasiswa_terlibat' => 'nullable|integer|min:0',
            'dosen_terlibat' => 'string',
            'mahasiswa_terlibat' => 'string',
            'anggaran_ukdw' => 'required|numeric',
            'hasil_pelaksanaan' => 'required',
            'tautan_link_kegiatan' => 'nullable|url',
            'dokumen_kegiatan' => 'nullable|file|mimes:pdf|max:5120', // max 5MB
        ], [
            'tautan_link_kegiatan.url' => 'Tautan kegiatan harus berupa URL yang valid',
            'dokumen_kegiatan.mimes' => 'Dokumen kegiatan harus berupa file PDF',
            'dokumen_kegiatan.max' => 'Ukuran file maksimal 5MB',
        ]);

        // Format angka
        if ($request->anggaran_ukdw) {
            $validated['anggaran_ukdw'] = str_replace('.', '', $request->anggaran_ukdw);
        }

        $pelaksanaan = PelaksanaanKerjaSama::findOrFail($id);

        // Handle file upload
        if ($request->hasFile('dokumen_kegiatan')) {
            // Hapus file lama jika ada
            if ($pelaksanaan->dokumen_kegiatan && Storage::disk('public')->exists($pelaksanaan->dokumen_kegiatan)) {
                Storage::disk('public')->delete($pelaksanaan->dokumen_kegiatan);
            }

            // Simpan file baru
            $file = $request->file('dokumen_kegiatan');
            $validated['dokumen_kegiatan'] = $file->store('dokumen_kegiatan', 'public');
        }

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
