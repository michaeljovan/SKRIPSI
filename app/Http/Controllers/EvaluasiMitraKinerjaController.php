<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiMitraKinerja;
use App\Models\RekapKerjaSama;
use Illuminate\Http\Request;

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

    public function create($id)
    {
        $rekap = RekapKerjaSama::findOrFail($id);
        return view('inputevaluasikerjasamakinerja', compact('rekap'));
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

        EvaluasiMitraKinerja::create($validated);

        $rekap = RekapKerjaSama::find($validated['rekap_id']);
        $rekap->update(['is_kinerja' => true]);

        return redirect()->back()->with('success', 'Evaluasi berhasil disimpan');
    }

    /**
     * Display a listing of the evaluations for admin view.
     */
    public function list()
    {
        $evaluations = EvaluasiMitraKinerja::latest()->paginate(10);
        return view('EvaluasiMitraKinerja.list', compact('evaluations'));
    }

    /**
     * Display the specified evaluation.
     */
    public function show(EvaluasiMitraKinerja $evaluasi)
    {
        return view('EvaluasiMitraKinerja.show', compact('evaluasi'));
    }
}
