<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// ====== SESUAIKAN impor model di bawah ini dengan project-mu ======
use App\Models\RekapKerjaSama;
use App\Models\EvaluasiMitraKinerjaPerorangan; // <- buat model/table ini sesuai migrasimu
use App\Models\PelaksanaanKerjaSama;           // <- jika nama model laporan beda, ganti di sini
// ================================================================

class EvaluasiMitraKinerjaPeroranganController extends Controller
{
    /**
     * Tampilkan form evaluasi perorangan
     * Route GET: EvaluasiMitraKinerjaPerorangan.create
     */
    public function create($id)
    {
        $rekap = \App\Models\RekapKerjaSama::with('laporanPelaksanaan')->findOrFail($id);

        // Ambil laporan terbaru dari relasi (entah hasOne/hasMany)
        $lap = $rekap->laporanPelaksanaan;
        if ($lap instanceof \Illuminate\Database\Eloquent\Collection) {
            // jika relasi hasMany
            $laporan = $lap->sortByDesc('id')->first();
        } else {
            // jika relasi hasOne
            $laporan = $lap;
        }

        // Build daftar dosen/mahasiswa
        $split = function (?string $s): array {
            if (!$s) return [];
            $arr = preg_split('/\r\n|\r|\n|,|;/', $s);
            return array_values(array_filter(array_map('trim', $arr), fn($v) => $v !== ''));
        };

        $dosenList     = $split(optional($laporan)->dosen_terlibat);
        $mahasiswaList = $split(optional($laporan)->mahasiswa_terlibat);

        return view('evaluasi_kinerja_perorangan_create', compact('rekap', 'laporan', 'dosenList', 'mahasiswaList'));
    }


    /**
     * Simpan evaluasi perorangan
     * Route POST: EvaluasiMitraKinerjaPerorangan.store
     */
    public function store(Request $request, $id)
    {
        $rekap = \App\Models\RekapKerjaSama::findOrFail($id);

        $map = [
            'Sangat Tinggi' => 5,
            'Tinggi' => 4,
            'Cukup' => 3,
            'Kurang' => 2,
            'Sangat Kurang' => 1
        ];

        $validated = $request->validate([
            'rekap_id'      => ['required', 'integer', 'in:' . $rekap->id],
            'pengisi_mitra' => ['required', 'string', 'max:255'],
            'items'         => ['required', 'array', 'min:1'],

            'items.*.tipe_responden' => ['required', 'in:dosen,mahasiswa'],
            'items.*.nama_responden' => ['required', 'string', 'max:255'],

            'items.*.integritas'       => ['required', 'in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang'],
            'items.*.keahlian'         => ['required', 'in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang'],
            'items.*.komunikasi'       => ['required', 'in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang'],
            'items.*.kerjasamatim'     => ['required', 'in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang'],
            'items.*.pengembangandiri' => ['required', 'in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang'],
            'items.*.kreativitas'      => ['required', 'in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang'],
            'items.*.bahasaasing'      => ['required', 'in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang'],
            'items.*.teknologi'        => ['required', 'in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang'],
            'items.*.manajerial'       => ['required', 'in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang'],
            'items.*.analisis'         => ['required', 'in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang'],
            'items.*.laporan'          => ['required', 'in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang'],
            'items.*.inovasi'          => ['required', 'in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang'],

            // pilih salah satu: kalau mau wajib isi, ganti 'nullable' -> 'required'
            'items.*.lainlainlabel'    => ['nullable', 'string', 'max:255'],
            'items.*.lainlainnilai'    => ['nullable', 'in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang'],

            'items.*.komentar'         => ['nullable', 'string'],
            'items.*.pdfFile'          => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        foreach ($validated['items'] as $idx => $it) {
            $data = [
                'rekap_id'       => $rekap->id,
                'tipe_responden' => $it['tipe_responden'],
                'nama_responden' => $it['nama_responden'],
                'pengisi_mitra'  => trim($validated['pengisi_mitra']),
                'lainlainlabel'  => $it['lainlainlabel'] ?? null,
                'komentar'       => $it['komentar'] ?? null,
                'submitted_at'   => now(),
            ];
            foreach (['integritas', 'keahlian', 'komunikasi', 'kerjasamatim', 'pengembangandiri', 'kreativitas', 'bahasaasing', 'teknologi', 'manajerial', 'analisis', 'laporan', 'inovasi', 'lainlainnilai'] as $f) {
                if (isset($it[$f]) && isset($map[$it[$f]])) {
                    $data[$f] = $map[$it[$f]];
                }
            }
            if ($request->hasFile("items.$idx.pdfFile")) {
                $data['lampiran_pdf_path'] = $request->file("items.$idx.pdfFile")
                    ->store('evaluasi_kinerja/perorangan', 'public');
            }
            \App\Models\EvaluasiMitraKinerjaPerorangan::create($data);
        }

        // opsional: tandai rekap punya evaluasi
        // \App\Models\RekapKerjaSama::where('id',$rekap->id)->update(['is_kinerja'=>true]);

        return back()->with('success', 'Semua evaluasi perorangan berhasil dikirim.');
    }


    /**
     * Helper: ubah string daftar nama menjadi array tertrim unik
     */
    private static function explodeToList(string $raw): array
    {
        if (trim($raw) === '') return [];
        // Pisahkan dengan koma atau titik-koma
        $parts = preg_split('/[;,]/', $raw);
        $parts = array_map(fn($v) => trim($v), $parts);
        $parts = array_filter($parts, fn($v) => $v !== '');
        $parts = array_values(array_unique($parts));
        return $parts;
    }
}
