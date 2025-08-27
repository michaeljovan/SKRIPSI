<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiMitraKinerja;
use App\Models\RekapKerjaSama;
use App\Models\EvaluasiKinerjaOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\MitraEvaluasiLinkMail;
use App\Mail\AdminOtpMail;

class EvaluasiMitraKinerjaController extends Controller
{
    /** List evaluasi (admin) */
    public function index(\Illuminate\Http\Request $request)
    {
        $q = \App\Models\EvaluasiMitraKinerja::query()
            // eager load: rekap + laporan pelaksanaan (hindari N+1)
            ->with(['rekapKerjasama.laporanPelaksanaan']);

        // Pencarian opsional (?s=keyword) pada No Dokumen & Mitra
        if ($request->filled('s')) {
            $s = trim((string) $request->input('s'));
            $q->where(function ($qq) use ($s) {
                $qq->where('nodok', 'like', "%{$s}%")
                    ->orWhere('mitra', 'like', "%{$s}%");
            });
        }

        // Page size opsional (?per_page=20), dibatasi agar aman
        $perPage = (int) $request->input('per_page', 10);
        if ($perPage < 1 || $perPage > 100) {
            $perPage = 10;
        }

        $evaluasi = $q->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString(); // pertahankan ?s= & ?per_page=

        return view('evaluasikerjasamakinerja', compact('evaluasi'));
    }


    /** Halaman gerbang OTP (mitra) */
    public function showOtpGate($rekapId)
    {
        $rekap = RekapKerjaSama::findOrFail($rekapId);
        return view('evaluasi_kinerja_otp_gate', compact('rekap'));
    }

    /** Verifikasi OTP dari mitra */
    public function verifyOtp(Request $request, $rekapId)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ], [
            'otp.size' => 'Kode OTP harus 6 digit.',
        ]);

        $rekap = RekapKerjaSama::findOrFail($rekapId);

        $maxHours = (int) env('EVAL_KINERJA_OTP_MAX_HOURS', 12);

        $recentOtps = EvaluasiKinerjaOtp::where('rekap_id', $rekap->id)
            ->where('created_at', '>=', now()->subHours($maxHours))
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $matched = null;
        foreach ($recentOtps as $o) {
            if (Hash::check($request->otp, $o->code_hash)) {
                $matched = $o;
                break;
            }
        }

        if (!$matched) {
            return back()->withErrors(['otp' => 'OTP tidak valid. Periksa kembali kode yang Anda masukkan.'])->withInput();
        }

        if (!is_null($matched->used_at)) {
            return back()->withErrors(['otp' => 'OTP sudah digunakan pada ' . $matched->used_at->format('d-m-Y H:i') . '. Silakan minta OTP baru.'])->withInput();
        }

        if ($matched->expires_at->lte(now())) {
            return back()->withErrors(['otp' => 'OTP sudah kedaluwarsa pada ' . $matched->expires_at->format('d-m-Y H:i') . '. Silakan minta OTP baru.'])->withInput();
        }

        if ($matched->created_at->lt(now()->subHours($maxHours))) {
            return back()->withErrors(['otp' => 'OTP lebih tua dari batas maksimal ' . $maxHours . ' jam. Silakan minta OTP baru.'])->withInput();
        }

        // Valid → tandai digunakan & buka akses
        $matched->update(['used_at' => now()]);
        session(['evaluasi_mitra_kinerja_allowed' => (int)$rekap->id]);

        return redirect()->route('EvaluasiMitraKinerja.create', ['id' => $rekap->id]);
    }

    /** Form evaluasi (mitra) */
    public function create($id)
    {
        // Gate OTP (lebih ramah: redirect ke gate jika invalid)
        $allowed = (int) session('evaluasi_mitra_kinerja_allowed');
        if ($allowed !== (int) $id) {
            return redirect()
                ->route('EvaluasiMitraKinerja.otpGate', ['rekapId' => $id])
                ->with('error', 'Sesi evaluasi tidak valid atau sudah kedaluwarsa. Silakan masukkan OTP lagi.');
        }

        $rekap   = RekapKerjaSama::with('laporanPelaksanaan')->findOrFail($id);
        $laporan = $rekap->laporanPelaksanaan; // bisa null

        // Split helper (koma / baris baru)
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

    /** Map nilai teks → angka (helper untuk unit test) */
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

    /** Simpan evaluasi (mitra) */
    public function store(Request $request)
    {
        // Gate OTP saat submit: ambil id dari route atau input
        $rekapIdFromRoute = $request->route('id');
        $rekapId          = (int) ($rekapIdFromRoute ?? $request->input('rekap_id'));
        $allowed          = (int) session('evaluasi_mitra_kinerja_allowed');

        if ($allowed !== $rekapId) {
            return redirect()
                ->route('EvaluasiMitraKinerja.otpGate', ['rekapId' => $rekapId ?: 0])
                ->with('error', 'Sesi evaluasi tidak valid atau sudah kedaluwarsa. Silakan masukkan OTP lagi.');
        }

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

            // --- TAMBAHAN: pengisi dari pihak mitra ---
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

        // Rapikan nama pengisi
        $validated['pengisi_mitra'] = trim($validated['pengisi_mitra']);

        // Konversi nilai teks → angka
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

        // Upload PDF (opsional)
        if ($request->hasFile('pdfFile')) {
            $validated['file_pdf'] = $request->file('pdfFile')->store('evaluasi_pdf', 'public');
        }

        // Simpan evaluasi
        EvaluasiMitraKinerja::create($validated);

        // Tandai rekap sudah punya evaluasi kinerja
        RekapKerjaSama::where('id', $validated['rekap_id'])->update(['is_kinerja' => true]);

        // (opsional) habiskan sesi agar tidak reuse di tab lain
        // session()->forget('evaluasi_mitra_kinerja_allowed');

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

            // Hapus file PDF jika ada
            if (!empty($evaluasi->file_pdf) && Storage::disk('public')->exists($evaluasi->file_pdf)) {
                Storage::disk('public')->delete($evaluasi->file_pdf);
            }

            $evaluasi->delete();

            // Set is_kinerja=false hanya jika tidak ada evaluasi lain untuk rekap ini
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
            'rekap'    => $evaluasi->rekapKerjasama ?? $evaluasi->rekap, // fallback jika nama relasi berbeda
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

        // Ganti PDF (opsional)
        if ($request->hasFile('pdfFile')) {
            if (!empty($evaluasi->file_pdf) && Storage::disk('public')->exists($evaluasi->file_pdf)) {
                Storage::disk('public')->delete($evaluasi->file_pdf);
            }
            $filename = 'eval_' . time() . '_' . Str::slug(pathinfo($request->file('pdfFile')->getClientOriginalName(), PATHINFO_FILENAME)) . '.pdf';
            $path = $request->file('pdfFile')->storeAs('evaluasi_pdf', $filename, 'public');
            $validated['file_pdf'] = $path; // simpan path relatif ke disk 'public'
        }

        $evaluasi->update($validated);

        return redirect()
            ->route('EvaluasiMitraKinerja.index')
            ->with('success', 'Evaluasi berhasil diperbarui.');
    }

    /**
     * Kirim tautan ke email mitra + OTP ke email admin
     * Panggil dari dashboard admin.
     */
    public function kirimLinkDanOtp(Request $request, $rekapId)
    {
        $rekap = RekapKerjaSama::findOrFail($rekapId);

        // Email admin penerima OTP
        $adminEmail = $request->user()->email
            ?? config('mail.admin_address')
            ?? config('mail.from.address');

        if (empty($adminEmail)) {
            return back()->with('error', 'Email admin tidak terkonfigurasi.');
        }

        // OTP 6 digit
        $plainOtp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // TTL & hard cap
        $ttlMinutes = (int) env('EVAL_KINERJA_OTP_TTL', 30);
        $maxHours   = (int) env('EVAL_KINERJA_OTP_MAX_HOURS', 12);

        $expiresAt  = now()->addMinutes($ttlMinutes);
        $hardCap    = now()->addHours($maxHours);
        if ($expiresAt->greaterThan($hardCap)) {
            $expiresAt = $hardCap;
        }

        // Simpan OTP
        EvaluasiKinerjaOtp::create([
            'rekap_id'      => $rekap->id,
            'code_hash'     => Hash::make($plainOtp),
            'expires_at'    => $expiresAt,
            'used_at'       => null,
            'sent_to_email' => $adminEmail,
        ]);

        // Tautan gate OTP (mitra klik link ini)
        $tautanGate = route('EvaluasiMitraKinerja.otpGate', ['rekapId' => $rekap->id]);

        // Email ke MITRA: hanya tautan gate
        if (!empty($rekap->email_pihak_mitra)) {
            Mail::to($rekap->email_pihak_mitra)->send(new MitraEvaluasiLinkMail($rekap, $tautanGate));
        }

        // Email ke ADMIN: kirim OTP (+ link gate sebagai referensi)
        Mail::to($adminEmail)->send(new AdminOtpMail($rekap, $plainOtp, $tautanGate, 'kinerja'));

        return back()->with('success', 'Tautan dikirim ke email mitra; OTP dikirim ke email admin.');
    }
}
