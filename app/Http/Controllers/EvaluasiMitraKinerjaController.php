<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiMitraKinerja;
use App\Models\RekapKerjaSama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\EvaluasiKinerjaOtp;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Mail;
use App\Mail\MitraEvaluasiLinkMail;
use App\Mail\AdminOtpMail;

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

    public function showOtpGate($rekapId)
    {
        $rekap = RekapKerjaSama::findOrFail($rekapId);
        return view('evaluasi_kinerja_otp_gate', compact('rekap'));
    }

    public function verifyOtp(Request $request, $rekapId)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ], [
            'otp.size' => 'Kode OTP harus 6 digit.',
        ]);

        $rekap = RekapKerjaSama::findOrFail($rekapId);

        // Hard cap umur OTP (default 12 jam)
        $maxHours = (int) env('EVAL_KINERJA_OTP_MAX_HOURS', 12);

        // Ambil beberapa OTP terbaru (12 jam terakhir), urutkan terbaru → terlama
        $recentOtps = EvaluasiKinerjaOtp::where('rekap_id', $rekap->id)
            ->where('created_at', '>=', now()->subHours($maxHours))
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        // Cari OTP yang hash-nya cocok
        $matched = null;
        foreach ($recentOtps as $o) {
            if (\Hash::check($request->otp, $o->code_hash)) {
                $matched = $o;
                break;
            }
        }

        // Tidak ada yang cocok
        if (!$matched) {
            return back()->withErrors([
                'otp' => 'OTP tidak valid. Periksa kembali kode yang Anda masukkan.'
            ])->withInput();
        }

        // Sudah pernah digunakan
        if (!is_null($matched->used_at)) {
            return back()->withErrors([
                'otp' => 'OTP sudah digunakan pada ' . $matched->used_at->format('d-m-Y H:i') . '. Silakan minta OTP baru.'
            ])->withInput();
        }

        // Kadaluarsa (berdasarkan kolom expires_at)
        if ($matched->expires_at->lte(now())) {
            return back()->withErrors([
                'otp' => 'OTP sudah kedaluwarsa pada ' . $matched->expires_at->format('d-m-Y H:i') . '. Silakan minta OTP baru.'
            ])->withInput();
        }

        // Guard ekstra: jangan terima OTP yang lebih tua dari hard cap
        if ($matched->created_at->lt(now()->subHours($maxHours))) {
            return back()->withErrors([
                'otp' => 'OTP lebih tua dari batas maksimal ' . $maxHours . ' jam. Silakan minta OTP baru.'
            ])->withInput();
        }

        // Valid → set used_at & buka akses
        $matched->update(['used_at' => now()]);

        session(['evaluasi_mitra_kinerja_allowed' => $rekap->id]);

        return redirect()->route('EvaluasiMitraKinerja.create', ['id' => $rekap->id]);
    }


    public function create($id)
    {
        // Batasi akses hanya yang sudah lolos OTP
        if (session('evaluasi_mitra_kinerja_allowed') != $id) {
            abort(403, 'Akses evaluasi tidak valid atau sudah kedaluwarsa.');
        }

        // AMBIL OBJEK REKAP & KIRIM KE VIEW
        $rekap = \App\Models\RekapKerjaSama::findOrFail($id);

        return view('inputevaluasikerjasamakinerja', compact('rekap'));
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
                'inovasi',
                'lainlainnilai'
            ] as $field
        ) {
            if (isset($validated[$field]) && isset($valueMap[$validated[$field]])) {
                $validated[$field] = $valueMap[$validated[$field]];
            }
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
            if (!is_numeric($id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID tidak valid'
                ], 400);
            }

            $evaluasi = EvaluasiMitraKinerja::with('rekapKerjasama')
                ->where('idkinerja', $id)
                ->first();

            if (!$evaluasi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data evaluasi tidak ditemukan'
                ], 404);
            }

            $rekap_id = $evaluasi->rekap_id;

            $evaluasi->delete();

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
            'pdfFile' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $map = [
            'Sangat Tinggi' => 5,
            'Tinggi' => 4,
            'Cukup' => 3,
            'Kurang' => 2,
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
            if ($evaluasi->file_pdf && Storage::exists('public/' . $evaluasi->file_pdf)) {
                Storage::delete('public/' . $evaluasi->file_pdf);
            }

            $file = $request->file('pdfFile');
            $filename = 'eval_' . time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.pdf';
            $path = $file->storeAs('public/evaluasi_pdf', $filename);
            $validated['file_pdf'] = str_replace('public/', '', $path);
        }

        $evaluasi->update($validated);

        return redirect()
            ->route('EvaluasiMitraKinerja.index')
            ->with('success', 'Evaluasi berhasil diperbarui.');
    }

    /**
     * Kirim tautan ke email mitra + kirim OTP ke email admin
     * Panggil method ini dari tombol/aksi di dashboard admin.
     */
    public function kirimLinkDanOtp(Request $request, $rekapId)
    {
        $rekap = RekapKerjaSama::findOrFail($rekapId);

        // A) Email admin penerima OTP
        $adminEmail = $request->user()->email
            ?? config('mail.admin_address')
            ?? config('mail.from.address');

        if (empty($adminEmail)) {
            return back()->with('error', 'Email admin tidak terkonfigurasi.');
        }

        // B) Generate OTP 6 digit
        $plainOtp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // C) TTL normal & hard cap (12 jam max dari .env)
        $ttlMinutes = (int) env('EVAL_KINERJA_OTP_TTL', 30);
        $maxHours   = (int) env('EVAL_KINERJA_OTP_MAX_HOURS', 12);

        $expiresAt  = now()->addMinutes($ttlMinutes);
        $hardCap    = now()->addHours($maxHours);
        if ($expiresAt->greaterThan($hardCap)) {
            $expiresAt = $hardCap;
        }

        // Simpan OTP
        $otp = new EvaluasiKinerjaOtp();
        $otp->rekap_id      = $rekap->id;
        $otp->code_hash     = \Hash::make($plainOtp);
        $otp->expires_at    = $expiresAt;
        $otp->used_at       = null;
        $otp->sent_to_email = $adminEmail;
        $otp->save();

        // D) Tautan gerbang OTP untuk MITRA
        $tautanGate = route('EvaluasiMitraKinerja.otpGate', ['rekapId' => $rekap->id]);

        // E) Email ke MITRA: link saja
        if (!empty($rekap->email_pihak_mitra)) {
            \Mail::to($rekap->email_pihak_mitra)
                ->send(new \App\Mail\MitraEvaluasiLinkMail($rekap, $tautanGate));
        }

        // F) Email ke ADMIN: OTP (+ opsional link gate)
        \Mail::to($adminEmail)
            ->send(new \App\Mail\AdminOtpMail($rekap, $plainOtp, $tautanGate, 'kinerja'));


        return back()->with('success', 'Tautan dikirim ke email mitra; OTP dikirim ke email admin.');
    }
}
