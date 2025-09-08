<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiMitraKinerja;
use App\Models\RekapKerjaSama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\MitraEvaluasiLinkMail;
use Carbon\Carbon;
use App\Models\EvaluasiLink;
use App\Models\EvaluasiMitraKinerjaPerorangan;

class EvaluasiMitraKinerjaController extends Controller
{
    /** List evaluasi (admin) */
    public function index(Request $request)
    {
        $s = trim((string) $request->input('s', ''));

        // page size terpisah agar tidak bentrok
        $perKes = (int) $request->input('per_kes', 10);
        $perPer = (int) $request->input('per_per', 10);
        $perKes = ($perKes < 1 || $perKes > 100) ? 10 : $perKes;
        $perPer = ($perPer < 1 || $perPer > 100) ? 10 : $perPer;

        // === K E S E L U R U H A N ===
        $qKes = EvaluasiMitraKinerja::query()
            ->with(['rekapKerjasama.laporanPelaksanaan'])
            ->orderByDesc('created_at');

        if ($s !== '') {
            $qKes->where(function ($qq) use ($s) {
                $qq->where('nodok', 'like', "%{$s}%")
                    ->orWhere('mitra', 'like', "%{$s}%");
            });
        }

        $evaluasiKes = $qKes->paginate($perKes, ['*'], 'page_kes')
            ->appends(['s' => $s, 'per_kes' => $perKes, 'per_per' => $perPer]);

        // === P E R O R A N G A N ===
        $qPer = EvaluasiMitraKinerjaPerorangan::query()
            ->with(['rekap.laporanPelaksanaan']) // pastikan relasi 'rekap' ada di model perorangan
            ->orderByDesc('submitted_at');

        if ($s !== '') {
            $qPer->where(function ($qq) use ($s) {
                $qq->where('nama_responden', 'like', "%{$s}%")
                    ->orWhere('pengisi_mitra', 'like', "%{$s}%");
            });
        }

        $evaluasiPer = $qPer->paginate($perPer, ['*'], 'page_per')
            ->appends(['s' => $s, 'per_kes' => $perKes, 'per_per' => $perPer]);

        return view('evaluasikerjasamakinerja', compact('evaluasiKes', 'evaluasiPer', 's', 'perKes', 'perPer'));
    }


    // === TOKEN HELPERS (tambahkan di dalam class EvaluasiMitraKinerjaController) ===
    protected function resolveTokenOrFail(string $plainToken, string $expectedContext = 'kinerja'): array
    {
        $hash = hash('sha256', $plainToken);

        /** @var \App\Models\EvaluasiLink|null $link */
        $link = EvaluasiLink::where('token_hash', $hash)
            ->when($expectedContext, fn($q) => $q->where('context', $expectedContext))
            ->first();

        if (!$link) {
            abort(404, 'Tautan tidak ditemukan.');
        }

        // validasi usable
        if (!$link->isUsable()) {
            if ($link->invalidated_at) abort(403, 'Tautan ini telah dinonaktifkan.');
            if ($link->used_at)        abort(410, 'Tautan ini sudah digunakan.');
            if ($link->expires_at && $link->expires_at->isPast()) abort(410, 'Tautan ini sudah kedaluwarsa.');
            abort(403, 'Tautan tidak valid.');
        }

        // Ambil rekap terkait
        $rekap = RekapKerjaSama::with('laporanPelaksanaan')->findOrFail($link->rekap_id);

        return [$link, $rekap];
    }

    protected function markLinkUsed(EvaluasiLink $link, Request $request): void
    {
        if (is_null($link->used_at)) {
            $link->update([
                'used_at'   => now(),
                // opsional: simpan metadata terakhir saat dipakai
                'request_ip' => $request->ip(),
                'user_agent' => substr($request->userAgent() ?? '', 0, 255),
            ]);
        }
    }

    public function createKeseluruhanByToken(string $token)
    {
        [$link, $rekap] = $this->resolveTokenOrFail($token, 'kinerja');

        $laporan = $rekap->laporanPelaksanaan; // bisa null
        $split = function (?string $s): array {
            if (!$s) return [];
            $arr = preg_split('/\r\n|\r|\n|,/', $s);
            return array_values(array_filter(array_map('trim', $arr), fn($v) => $v !== ''));
        };
        $dosenList      = $split(optional($laporan)->dosen_terlibat);
        $mahasiswaList  = $split(optional($laporan)->mahasiswa_terlibat);
        $dosenCount     = optional($laporan)->jumlah_dosen_terlibat ?? count($dosenList);
        $mahasiswaCount = optional($laporan)->jumlah_mahasiswa_terlibat ?? count($mahasiswaList);

        // KIRIM token ke view → <input type="hidden" name="token" value="{{ $token }}">
        return view('inputevaluasikerjasamakinerja', compact(
            'rekap',
            'laporan',
            'dosenList',
            'mahasiswaList',
            'dosenCount',
            'mahasiswaCount',
            'token'
        ));
    }

    public function storeKeseluruhanByToken(Request $request, string $token)
    {
        [$link, $rekap] = $this->resolveTokenOrFail($token, 'kinerja');

        // Validasi sama seperti store() biasa, tapi rekap_id dikunci dari token
        $valueMap = [
            'Sangat Tinggi' => 5,
            'Tinggi'        => 4,
            'Cukup'         => 3,
            'Kurang'        => 2,
            'Sangat Kurang' => 1,
        ];

        $validated = $request->validate([
            // tidak perlu terima rekap_id dari user—kita set manual dari token
            'nodok'    => 'required|string|max:255',
            'mitra'    => 'required|string|max:255',
            'pengisi_mitra' => 'required|string|max:100',

            'integritas'        => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'keahlian'          => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'komunikasi'        => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'kerjasamatim'      => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'pengembangandiri'  => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'kreativitas'       => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'bahasaasing'       => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'teknologi'         => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'manajerial'        => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'analisis'          => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'laporan'           => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'inovasi'           => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',

            'lainlainlabel' => 'nullable|string|max:255',
            'lainlainnilai' => 'nullable|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',

            'komentar' => 'nullable|string',
            'pdfFile'  => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $validated['rekap_id']      = $rekap->id;  // kunci dari token
        $validated['pengisi_mitra'] = trim($validated['pengisi_mitra']);

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
            ] as $f
        ) {
            if (isset($validated[$f]) && isset($valueMap[$validated[$f]])) {
                $validated[$f] = $valueMap[$validated[$f]];
            }
        }

        if ($request->hasFile('pdfFile')) {
            $validated['file_pdf'] = $request->file('pdfFile')->store('evaluasi_pdf', 'public');
        }

        EvaluasiMitraKinerja::create($validated);
        RekapKerjaSama::where('id', $rekap->id)->update(['is_kinerja' => true]);

        // K O N S U M S I  T O K E N
        $this->markLinkUsed($link, $request);

        return redirect()->route('EvaluasiMitra.thanks') // opsional halaman “terima kasih”
            ->with('success', 'Evaluasi berhasil disimpan.');
    }


    /** Form evaluasi KINERJA (mitra) — TANPA OTP */
    public function create($id)
    {
        $rekap   = RekapKerjaSama::with('laporanPelaksanaan')->findOrFail($id);
        $laporan = $rekap->laporanPelaksanaan; // bisa null

        $split = function (?string $s): array {
            if (!$s) return [];
            $arr = preg_split('/\r\n|\r|\n|,/', $s);
            return array_values(array_filter(array_map('trim', $arr), fn($v) => $v !== ''));
        };

        $dosenList      = $split(optional($laporan)->dosen_terlibat);
        $mahasiswaList  = $split(optional($laporan)->mahasiswa_terlibat);
        $dosenCount     = $laporan->jumlah_dosen_terlibat ?? count($dosenList);
        $mahasiswaCount = $laporan->jumlah_mahasiswa_terlibat ?? count($mahasiswaList);

        return view('inputevaluasikerjasamakinerja', compact(
            'rekap',
            'laporan',
            'dosenList',
            'mahasiswaList',
            'dosenCount',
            'mahasiswaCount'
        ));
    }

    public function createPeroranganByToken(string $token)
    {
        [$link, $rekap] = $this->resolveTokenOrFail($token, 'kinerja');

        $laporan = $rekap->laporanPelaksanaan;
        if ($laporan instanceof \Illuminate\Database\Eloquent\Collection) {
            $laporan = $laporan->sortByDesc('id')->first();
        }

        $split = function (?string $s): array {
            if (!$s) return [];
            $arr = preg_split('/\r\n|\r|\n|,|;/', $s);
            return array_values(array_filter(array_map('trim', $arr), fn($v) => $v !== ''));
        };
        $dosenList     = $split(optional($laporan)->dosen_terlibat);
        $mahasiswaList = $split(optional($laporan)->mahasiswa_terlibat);

        // gunakan view perorangan kamu + kirim token
        return view('evaluasi_kinerja_perorangan_create', compact('rekap', 'laporan', 'dosenList', 'mahasiswaList', 'token'));
    }

    public function storePeroranganByToken(Request $request, string $token)
    {
        [$link, $rekap] = $this->resolveTokenOrFail($token, 'kinerja');

        $map = [
            'Sangat Tinggi' => 5,
            'Tinggi'        => 4,
            'Cukup'         => 3,
            'Kurang'        => 2,
            'Sangat Kurang' => 1
        ];

        $validated = $request->validate([
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
            EvaluasiMitraKinerjaPerorangan::create($data);
        }

        // K O N S U M S I  T O K E N
        $this->markLinkUsed($link, $request);

        return redirect()->route('EvaluasiMitra.thanks') // opsional
            ->with('success', 'Semua evaluasi perorangan berhasil dikirim.');
    }


    /** Map nilai teks → angka */
    private function mapNilai($value)
    {
        $valueMap = [
            'Sangat Tinggi' => 5,
            'Tinggi'        => 4,
            'Cukup'         => 3,
            'Kurang'        => 2,
            'Sangat Kurang' => 1
        ];
        return $valueMap[$value] ?? null;
    }

    /** Simpan evaluasi KINERJA (mitra) — TANPA OTP */
    public function store(Request $request)
    {
        // Ambil id dari route atau input
        $rekapIdFromRoute = $request->route('id');
        $rekapId          = (int) ($rekapIdFromRoute ?? $request->input('rekap_id'));

        $valueMap = [
            'Sangat Tinggi' => 5,
            'Tinggi'        => 4,
            'Cukup'         => 3,
            'Kurang'        => 2,
            'Sangat Kurang' => 1,
        ];

        $validated = $request->validate([
            'rekap_id' => 'required|exists:rekapkerjasama,id',
            'nodok'    => 'required|string|max:255',
            'mitra'    => 'required|string|max:255',

            'pengisi_mitra' => 'required|string|max:100',

            'integritas'        => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'keahlian'          => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'komunikasi'        => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'kerjasamatim'      => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'pengembangandiri'  => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'kreativitas'       => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'bahasaasing'       => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'teknologi'         => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'manajerial'        => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'analisis'          => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'laporan'           => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'inovasi'           => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',

            'lainlainlabel' => 'nullable|string|max:255',
            'lainlainnilai' => 'nullable|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',

            'komentar' => 'nullable|string',
            'pdfFile'  => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $validated['pengisi_mitra'] = trim($validated['pengisi_mitra']);

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
            if (isset($validated[$field]) && isset($valueMap[$validated[$field]])) {
                $validated[$field] = $valueMap[$validated[$field]];
            }
        }

        if ($request->hasFile('pdfFile')) {
            $validated['file_pdf'] = $request->file('pdfFile')->store('evaluasi_pdf', 'public');
        }

        EvaluasiMitraKinerja::create($validated);

        RekapKerjaSama::where('id', $validated['rekap_id'])->update(['is_kinerja' => true]);

        return redirect()->back()->with('success', 'Evaluasi berhasil disimpan');
    }

    /** Hapus evaluasi (admin) */
    public function delete($id)
    {
        try {
            if (!is_numeric($id)) {
                return response()->json(['success' => false, 'message' => 'ID tidak valid'], 400);
            }

            $evaluasi = EvaluasiMitraKinerja::with('rekapKerjasama')
                ->where('idkinerja', $id)
                ->first();

            if (!$evaluasi) {
                return response()->json(['success' => false, 'message' => 'Data evaluasi tidak ditemukan'], 404);
            }

            $rekapId = $evaluasi->rekap_id;

            if (!empty($evaluasi->file_pdf) && Storage::disk('public')->exists($evaluasi->file_pdf)) {
                Storage::disk('public')->delete($evaluasi->file_pdf);
            }

            $evaluasi->delete();

            if ($rekapId) {
                $masihAda = EvaluasiMitraKinerja::where('rekap_id', $rekapId)->exists();
                if (!$masihAda) {
                    RekapKerjaSama::where('id', $rekapId)->update(['is_kinerja' => false]);
                }
            }

            return response()->json(['success' => true, 'message' => 'Hasil evaluasi berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus hasil evaluasi: ' . $e->getMessage()], 500);
        }
    }

    /** Form edit evaluasi (admin) */
    public function edit($id)
    {
        $evaluasi = EvaluasiMitraKinerja::where('idkinerja', $id)->firstOrFail();

        return view('evaluasikerjasamakinerjaedit', [
            'evaluasi' => $evaluasi,
            'rekap'    => $evaluasi->rekapKerjasama ?? $evaluasi->rekap,
        ]);
    }

    /** Update evaluasi (admin) */
    public function update(Request $request, $id)
    {
        $evaluasi = EvaluasiMitraKinerja::where('idkinerja', $id)->firstOrFail();

        $validated = $request->validate([
            'integritas'        => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'keahlian'          => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'komunikasi'        => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'kerjasamatim'      => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'pengembangandiri'  => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'kreativitas'       => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'bahasaasing'       => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'teknologi'         => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'manajerial'        => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'analisis'          => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'laporan'           => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'inovasi'           => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'lainlainlabel'     => 'nullable|string|max:255',
            'lainlainnilai'     => 'nullable|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'komentar'          => 'nullable|string',
            'pdfFile'           => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $map = [
            'Sangat Tinggi' => 5,
            'Tinggi'        => 4,
            'Cukup'         => 3,
            'Kurang'        => 2,
            'Sangat Kurang' => 1
        ];

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

        if ($request->hasFile('pdfFile')) {
            if (!empty($evaluasi->file_pdf) && Storage::disk('public')->exists($evaluasi->file_pdf)) {
                Storage::disk('public')->delete($evaluasi->file_pdf);
            }
            $filename = 'eval_' . time() . '_' . Str::slug(pathinfo($request->file('pdfFile')->getClientOriginalName(), PATHINFO_FILENAME)) . '.pdf';
            $path = $request->file('pdfFile')->storeAs('evaluasi_pdf', $filename, 'public');
            $validated['file_pdf'] = $path;
        }

        $evaluasi->update($validated);

        return redirect()->route('EvaluasiMitraKinerja.index')->with('success', 'Evaluasi berhasil diperbarui.');
    }

    /**
     * Kirim tautan ke email mitra (TANPA OTP) — menuju halaman pilihan
     */
    /**
     * Kirim tautan ke email mitra (TANPA OTP) — menuju halaman pilihan
     */
    public function kirimLinkDanOtp(Request $request, $rekapId)
    {
        $rekap = RekapKerjaSama::findOrFail($rekapId);

        $reqEmail   = trim((string) $request->input('email_mitra', ''));
        $modelEmail = trim((string) ($rekap->email_mitra ?? $rekap->email_pihak_mitra ?? ''));
        $toEmail    = $reqEmail !== '' ? $reqEmail : $modelEmail;

        if ($toEmail === '' || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            return back()->withErrors(['email_mitra' => 'Email mitra belum diisi/invalid.']);
        }

        $expiresAt  = Carbon::now()->addHours(24);

        // Buat token acak & simpan hash
        $plainToken = Str::random(64);
        $tokenHash  = hash('sha256', $plainToken);

        EvaluasiLink::create([
            'rekap_id'            => $rekap->id,
            'context'             => 'kinerja',
            'token_hash'          => $tokenHash,
            'expires_at'          => $expiresAt,
            'used_at'             => null,
            'invalidated_at'      => null,
            'sent_to_email'       => $toEmail,
            'created_by_staff_id' => auth()->id(),
            'request_ip'          => $request->ip(),
            'user_agent'          => substr($request->userAgent() ?? '', 0, 255),
        ]);

        // Kirim link ke halaman PILIHAN berbasis token
        $signedUrl = route('EvaluasiMitraKinerja.pilihan.token', ['token' => $plainToken]);

        Mail::to($toEmail)->send(new \App\Mail\MitraEvaluasiLinkMail(
            $rekap,
            $signedUrl,
            $expiresAt,
            'kinerja'
        ));

        return back()->with(
            'success',
            'Link evaluasi kinerja terkirim ke ' . $toEmail .
                ' (berlaku s.d. ' . $expiresAt->timezone('Asia/Jakarta')->format('d/m/Y H:i') . ' WIB).'
        );
    }


    // helper NON-STRICT: untuk halaman pilihan (jangan abort kalau sudah used/expired)
    protected function resolveTokenForDisplay(string $plainToken, ?string $expectedContext = null): array
    {
        $hash = hash('sha256', $plainToken);

        /** @var \App\Models\EvaluasiLink|null $link */
        $link = \App\Models\EvaluasiLink::where('token_hash', $hash)
            ->when($expectedContext, fn($q) => $q->where('context', $expectedContext))
            ->first();

        if (!$link) {
            abort(404, 'Tautan tidak ditemukan.');
        }

        $rekap = \App\Models\RekapKerjaSama::with('laporanPelaksanaan')->findOrFail($link->rekap_id);

        // hitung status tanpa abort
        $isUsable = method_exists($link, 'isUsable') ? $link->isUsable() : (
            is_null($link->used_at) &&
            is_null($link->invalidated_at) &&
            (!$link->expires_at instanceof \Carbon\Carbon || $link->expires_at->isFuture())
        );

        $reason = null;
        if (!$isUsable) {
            if ($link->invalidated_at)                         $reason = 'Tautan ini telah dinonaktifkan oleh sistem.';
            elseif ($link->used_at)                            $reason = 'Tautan ini sudah digunakan.';
            elseif ($link->expires_at && $link->expires_at->isPast()) $reason = 'Tautan ini sudah kedaluwarsa.';
            else                                               $reason = 'Tautan tidak valid.';
        }

        return [$link, $rekap, $isUsable, $reason];
    }


    // Halaman pilihan (by token) — NON-STRICT: selalu render, tapi tombol bisa disabled
    public function pilihanByToken(string $token)
    {
        [$link, $rekap, $isUsable, $reason] = $this->resolveTokenForDisplay($token, 'kinerja');

        return view('evaluasikinerjapilihan', [
            'rekap'          => $rekap,
            'token'          => $token,
            'isUsable'       => $isUsable,
            'reason'         => $reason,
            'expiresAt'      => $link->expires_at,
            'usedAt'         => $link->used_at,
            'invalidatedAt'  => $link->invalidated_at,
            'link'           => $link,
        ]);
    }


    /** Halaman pilihan (keseluruhan / perorangan) — TANPA OTP */
    /** Halaman pilihan (Keseluruhan / Perorangan) — TANPA OTP */
    public function pilihanForm($rekapId)
    {
        $rekap = RekapKerjaSama::findOrFail($rekapId);

        // hitung status usability link
        $status = $this->resolveKinerjaLinkStatus((int) $rekapId);

        // kirim ke view 'evaluasikinerjapilihan' (Blade-mu yang menampilkan dua tombol)
        return view('evaluasikinerjapilihan', array_merge(
            ['rekap' => $rekap],
            $status // -> isUsable, reason, expiresAt, usedAt, invalidatedAt, link
        ));
    }



    protected function resolveKinerjaLinkStatus(int $rekapId): array
    {
        $isUsable      = true;
        $reason        = null;
        $expiresAt     = null;
        $usedAt        = null;
        $invalidatedAt = null;
        $link          = null;

        // 1) Cek link terbaru pada tabel evaluasi_links (jika tersedia)
        if (class_exists(EvaluasiLink::class)) {
            $link = EvaluasiLink::where('rekap_id', $rekapId)
                ->where(function ($q) {
                    // kalau kamu menyimpan context, aktifkan filter ini:
                    $q->where('context', 'kinerja');
                })
                ->latest('id')
                ->first();

            if ($link) {
                $expiresAt     = $link->expires_at;
                $usedAt        = $link->used_at;
                $invalidatedAt = $link->invalidated_at;

                if (method_exists($link, 'isUsable')) {
                    $isUsable = $link->isUsable();
                } else {
                    $isUsable = is_null($usedAt)
                        && is_null($invalidatedAt)
                        && (!$expiresAt instanceof Carbon || $expiresAt->isFuture());
                }

                if (!$isUsable) {
                    if ($invalidatedAt)                       $reason = 'Tautan ini telah dinonaktifkan oleh sistem.';
                    elseif ($usedAt)                          $reason = 'Tautan ini sudah digunakan sebelumnya.';
                    elseif ($expiresAt && $expiresAt->isPast()) $reason = 'Tautan ini sudah kedaluwarsa.';
                    else                                      $reason = 'Tautan tidak valid.';
                }
            }
        }

        // 2) Fallback: jika tidak ada EvaluasiLink atau masih usable, cek apakah sudah ada isian
        if ($isUsable) {
            $sudahKes = EvaluasiMitraKinerja::where('rekap_id', $rekapId)->exists();
            $sudahPer = class_exists(EvaluasiMitraKinerjaPerorangan::class)
                ? EvaluasiMitraKinerjaPerorangan::where('rekap_id', $rekapId)->exists()
                : false;

            if ($sudahKes || $sudahPer) {
                $isUsable = false;
                $reason   = 'Form evaluasi kinerja untuk rekap ini sudah pernah diisi.';
            }
        }

        return compact('isUsable', 'reason', 'expiresAt', 'usedAt', 'invalidatedAt', 'link');
    }
}
