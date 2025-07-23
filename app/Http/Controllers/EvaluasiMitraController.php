<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiMitra;
use App\Models\RekapKerjaSama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EvaluasiMitraController extends Controller
{
    /*** Display the evaluation form.*/

    public function index()
    {
        $evaluasimitra = EvaluasiMitra::with('rekapKerjasama')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('evaluasikerjasamamitra', ['evaluasimitra' => $evaluasimitra]);
    }

    public function create($id)
    {
        $rekap = RekapKerjaSama::findOrFail($id);
        return view('inputevaluasikerjasamamitra', compact('rekap'));
    }
    public function store(Request $request)
    {
        // Map text values to numbers
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

            // Change validation to accept the text values
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

        // Convert text values to numbers before saving
        $validated['integritas'] = $valueMap[$validated['integritas']];
        $validated['keahlian'] = $valueMap[$validated['keahlian']];
        $validated['komunikasi'] = $valueMap[$validated['komunikasi']];
        $validated['kerjasamatim'] = $valueMap[$validated['kerjasamatim']];
        $validated['pengembangandiri'] = $valueMap[$validated['pengembangandiri']];
        $validated['kreativitas'] = $valueMap[$validated['kreativitas']];
        $validated['bahasaasing'] = $valueMap[$validated['bahasaasing']];
        $validated['teknologi'] = $valueMap[$validated['teknologi']];
        $validated['manajerial'] = $valueMap[$validated['manajerial']];
        $validated['analisis'] = $valueMap[$validated['analisis']];
        $validated['laporan'] = $valueMap[$validated['laporan']];
        $validated['inovasi'] = $valueMap[$validated['inovasi']];

        if (isset($validated['lainlainnilai'])) {
            $validated['lainlainnilai'] = $valueMap[$validated['lainlainnilai']];
        }

        if ($request->hasFile('pdfFile')) {
            $path = $request->file('pdfFile')->store('evaluasi_pdf', 'public');
            $validated['file_pdf'] = $path;
        }

        EvaluasiMitra::create($validated);

        $rekap = RekapKerjaSama::find($validated['rekap_id']);
        $rekap->update(['is_mitra' => true]);

        return redirect()->back()->with('success', 'Evaluasi berhasil disimpan');
    }

    // app/Http/Controllers/EvaluasiMitraController.php

    public function delete($id)
    {
        try {
            if (!is_numeric($id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID tidak valid'
                ], 400);
            }

            // Change to use 'idmitra' instead of 'idkinerja'
            $evaluasi = EvaluasiMitra::with('rekapKerjasama')
                ->where('idmitra', $id)
                ->first();

            if (!$evaluasi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data evaluasi mitra tidak ditemukan'
                ], 404);
            }

            $rekap_id = $evaluasi->rekap_id;
            $evaluasi->delete();

            if ($rekap_id) {
                RekapKerjaSama::where('id', $rekap_id)
                    ->update(['is_mitra' => false]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Evaluasi mitra berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus evaluasi mitra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $evaluasi = EvaluasiMitra::findOrFail($id);

        $rekap = RekapKerjasama::findOrFail($evaluasi->rekap_id);

        return view('evaluasikerjasamamitraedit', compact('evaluasi', 'rekap'));
    }

    public function update(Request $request, $id)
    {
        $evaluasi = EvaluasiMitra::where('idmitra', $id)->firstOrFail();

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

        // Handle file upload PDF
        if ($request->hasFile('pdfFile')) {
            // Hapus file lama jika ada
            if ($evaluasi->file_pdf && Storage::exists('public/' . $evaluasi->file_pdf)) {
                Storage::delete('public/' . $evaluasi->file_pdf);
            }

            // Upload file baru
            $file = $request->file('pdfFile');
            $filename = 'eval_mitra_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/evaluasi_mitra', $filename);
            $validated['file_pdf'] = str_replace('public/', '', $path);
        }

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

        // Update data
        $evaluasi->update($validated);

        return redirect()
            ->route('EvaluasiMitra.index')
            ->with('success', 'Evaluasi mitra berhasil diperbarui');
    }
}
