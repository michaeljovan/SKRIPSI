<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiMitraKinerja;
use App\Models\RekapKerjaSama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EvaluasiMitraKinerjaController extends Controller
{
    /*** Display the evaluation form.*/

    public function index()
    {
        $evaluasi = EvaluasiMitraKinerja::with('rekapKerjasama')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('evaluasikerjasamakinerja', ['evaluasi' => $evaluasi]);
    }


    // buat testing unit
    private function mapNilai($value)
    {
        $valueMap = [
            'Sangat Tinggi' => 5,
            'Tinggi' => 4,
            'Cukup' => 3,
            'Kurang' => 2,
            'Sangat Kurang' => 1
        ];
        return $valueMap[$value] ?? null;
    }

    public function create($id)
    {
        $rekap = RekapKerjaSama::findOrFail($id);
        return view('inputevaluasikerjasamakinerja', compact('rekap'));
    }
      
    public function store(Request $request)
    {
        $valueMap = [
            'Sangat Tinggi' => 5,
            'Tinggi' => 4,
            'Cukup' => 3,
            'Kurang' => 2,
            'Sangat Kurang' => 1
        ];

        $validated = $request->validate([
            'rekap_id' => 'required|exists:rekapkerjasama,id',
            'nodok' => 'required|string|max:255',
            'mitra' => 'required|string|max:255',

            'integritas' => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'keahlian' => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'komunikasi' => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'kerjasamatim' => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'pengembangandiri' => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'kreativitas' => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'bahasaasing' => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'teknologi' => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'manajerial' => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'analisis' => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'laporan' => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'inovasi' => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',

            'lainlainlabel' => 'nullable|string|max:255',
            'lainlainnilai' => 'nullable|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',

            'komentar' => 'nullable|string',
            'pdfFile' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        // Konversi nilai teks ke angka
        foreach (
            [
                'integritas',
                'keahlian',
                'komunikasi',
                'kerjasamatim',
                'pengembangandiri',
                'kreativitas',
                'bahasaasing',
                'teknologi',
                'manajerial',
                'analisis',
                'laporan',
                'inovasi'
            ] as $field
        ) {
            $validated[$field] = $valueMap[$validated[$field]];
        }

        if (isset($validated['lainlainnilai'])) {
            $validated['lainlainnilai'] = $valueMap[$validated['lainlainnilai']];
        }

        if ($request->hasFile('pdfFile')) {
            $path = $request->file('pdfFile')->store('evaluasi_pdf', 'public');
            $validated['file_pdf'] = $path;
        }

        EvaluasiMitraKinerja::create($validated);

        RekapKerjaSama::where('id', $validated['rekap_id'])
            ->update(['is_kinerja' => true]);

        return redirect()->back()->with('success', 'Evaluasi berhasil disimpan');
    }


    public function delete($id)
    {
        try {
            // Validasi ID
            if (!is_numeric($id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID tidak valid'
                ], 400);
            }

            // Cari data evaluasi
            $evaluasi = EvaluasiMitraKinerja::with('rekapKerjasama')
                ->where('idkinerja', $id)
                ->first();

            if (!$evaluasi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data evaluasi tidak ditemukan'
                ], 404);
            }

            // Simpan rekap_id sebelum dihapus
            $rekap_id = $evaluasi->rekap_id;

            // Hapus data evaluasi
            $evaluasi->delete();

            // Update is_kinerja di rekapkerjasama
            if ($rekap_id) {
                RekapKerjaSama::where('id', $rekap_id)
                    ->update(['is_kinerja' => false]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Hasil evaluasi berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus hasil evaluasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $evaluasi = EvaluasiMitraKinerja::where('idkinerja', $id)->firstOrFail();

        return view('evaluasikerjasamakinerjaedit', [
            'evaluasi' => $evaluasi,
            'rekap' => $evaluasi->rekap
        ]);
    }


    public function update(Request $request, $id)
    {
        $evaluasi = EvaluasiMitraKinerja::where('idkinerja', $id)->firstOrFail();

        // Validasi input
        $validated = $request->validate([
            'integritas' => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'keahlian' => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'komunikasi' => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'kerjasamatim' => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'pengembangandiri' => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'kreativitas' => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'bahasaasing' => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'teknologi' => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'manajerial' => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'analisis' => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'laporan' => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'inovasi' => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'lainlainlabel' => 'nullable|string|max:255',
            'lainlainnilai' => 'nullable|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'komentar' => 'nullable|string',
            'pdfFile' => 'nullable|file|mimes:pdf|max:5120', // 5MB
        ]);

        // Konversi nilai teks ke angka
        $map = [
            'Sangat Tinggi' => 5,
            'Tinggi' => 4,
            'Cukup' => 3,
            'Kurang' => 2,
            'Sangat Kurang' => 1
        ];

        // Konversi nilai teks ke angka
        foreach (
            [
                'integritas',
                'keahlian',
                'komunikasi',
                'kerjasamatim',
                'pengembangandiri',
                'kreativitas',
                'bahasaasing',
                'teknologi',
                'manajerial',
                'analisis',
                'laporan',
                'inovasi',
                'lainlainnilai'
            ] as $field
        ) {
            if (isset($validated[$field])) {
                $validated[$field] = $map[$validated[$field]] ?? null;
            }
        }

        // Handle file upload
        if ($request->hasFile('pdfFile')) {
            // Hapus file lama jika ada
            if ($evaluasi->file_pdf && Storage::exists('public/' . $evaluasi->file_pdf)) {
                Storage::delete('public/' . $evaluasi->file_pdf);
            }

            // Upload file baru
            $file = $request->file('pdfFile');
            $filename = 'eval_' . time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.pdf';
            $path = $file->storeAs('public/evaluasi_pdf', $filename);
            $validated['file_pdf'] = str_replace('public/', '', $path);
        }

        // Update data
        $evaluasi->update($validated);

        return redirect()
            ->route('EvaluasiMitraKinerja.index')
            ->with('success', 'Evaluasi berhasil diperbarui.');
    }
}
