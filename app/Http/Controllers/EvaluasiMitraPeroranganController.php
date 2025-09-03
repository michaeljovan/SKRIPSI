<?php

namespace App\Http\Controllers;

use App\Models\RekapKerjaSama;
use App\Models\EvaluasiMitraPerorangan;
use Illuminate\Http\Request;

class EvaluasiMitraPeroranganController extends Controller
{
    /** Tampilkan form perorangan (multi-orang) */
    public function create($id)
    {
        $rekap = RekapKerjaSama::with('laporanPelaksanaan')->findOrFail($id);
        $laporan = $rekap->laporanPelaksanaan;

        // siapkan nama terlibat (dosen/mahasiswa)
        $split = function (?string $s): array {
            if (!$s) return [];
            $arr = preg_split('/\r\n|\r|\n|,/', $s);
            return array_values(array_filter(array_map('trim', $arr), fn($v) => $v !== ''));
        };

        $dosenList = $split(optional($laporan)->dosen_terlibat);
        $mahasiswaList = $split(optional($laporan)->mahasiswa_terlibat);

        // View yang sudah kamu buat sebelumnya
        return view('evaluasimitra_perorangan', compact('rekap', 'dosenList', 'mahasiswaList'));
    }

    /** Simpan semua isian perorangan */
    public function store(Request $request, $id)
    {
        $rekap = RekapKerjaSama::findOrFail($id);

        // Validasi
        $request->validate([
            'rekap_id' => 'required|in:'.$rekap->id, // pastikan sesuai URL
            'pengisi_mitra' => 'required|string|max:100',

            'items' => 'required|array|min:1',
            'items.*.tipe_responden' => 'required|in:dosen,mahasiswa',
            'items.*.nama_responden' => 'required|string|max:255',

            'items.*.integritas'       => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'items.*.keahlian'         => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'items.*.komunikasi'       => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'items.*.kerjasamatim'     => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'items.*.pengembangandiri' => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'items.*.kreativitas'      => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'items.*.bahasaasing'      => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',

            'items.*.pdfFile' => 'nullable|file|mimes:pdf|max:5120',
            'items.*.komentar' => 'nullable|string',
        ]);

        $map = [
            'Sangat Tinggi' => 5,
            'Tinggi'        => 4,
            'Cukup'         => 3,
            'Kurang'        => 2,
            'Sangat Kurang' => 1,
        ];

        foreach ($request->input('items', []) as $idx => $it) {
            $data = [
                'rekap_id'       => $rekap->id,
                'tipe_responden' => $it['tipe_responden'],
                'nama_responden' => $it['nama_responden'],
                'pengisi_mitra'  => trim($request->input('pengisi_mitra')),
                'komentar'       => $it['komentar'] ?? null,
                'submitted_at'   => now(),
            ];

            foreach ([
                'integritas','keahlian','komunikasi','kerjasamatim',
                'pengembangandiri','kreativitas','bahasaasing',
            ] as $f) {
                $data[$f] = $map[$it[$f]] ?? null;
            }

            // file per orang (opsional)
            if ($request->hasFile("items.$idx.pdfFile")) {
                $data['lampiran_pdf_path'] = $request->file("items.$idx.pdfFile")
                    ->store('evaluasi_mitra/perorangan', 'public');
            }

            EvaluasiMitraPerorangan::create($data);
        }

        // tandai rekap sudah ada evaluasi mitra
        $rekap->update(['is_mitra' => true]);

        return back()->with('success', 'Semua evaluasi perorangan berhasil dikirim.');
    }
}
