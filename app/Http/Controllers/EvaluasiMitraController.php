<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiMitra;
use App\Models\RekapKerjaSama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\EvaluasiKinerjaOtp;

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
        if (session('evaluasi_mitra_allowed') != $id) {
            abort(403, 'Akses evaluasi tidak valid atau sudah kedaluwarsa.');
        }

        $rekap = RekapKerjaSama::findOrFail($id);
        return view('inputevaluasikerjasamamitra', compact('rekap')); // ganti sesuai nama blade kamu
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


    public function showOtpGate($rekapId)
    {
        $rekap = RekapKerjaSama::findOrFail($rekapId);
        return view('evaluasi_mitra_otp_gate', compact('rekap'));
    }

    public function verifyOtp(Request $request, $rekapId)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ], ['otp.size' => 'Kode OTP harus 6 digit.']);

        $rekap = RekapKerjaSama::findOrFail($rekapId);

        // Hard cap umur OTP (default 12 jam)
        $maxHours = (int) env('EVAL_KINERJA_OTP_MAX_HOURS', 12);

        // Ambil beberapa OTP terbaru (12 jam terakhir), lalu cocokan hash
        $recentOtps = EvaluasiKinerjaOtp::where('rekap_id', $rekap->id)
            ->where('created_at', '>=', now()->subHours($maxHours))
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $matched = null;
        foreach ($recentOtps as $o) {
            if (\Hash::check($request->otp, $o->code_hash)) {
                $matched = $o;
                break;
            }
        }

        if (!$matched) {
            return back()->withErrors(['otp' => 'OTP tidak valid. Periksa kembali kode yang Anda masukkan.'])
                ->withInput();
        }

        if (!is_null($matched->used_at)) {
            return back()->withErrors(['otp' => 'OTP sudah digunakan pada ' . $matched->used_at->format('d-m-Y H:i') . '. Silakan minta OTP baru.'])
                ->withInput();
        }

        if ($matched->expires_at->lte(now())) {
            return back()->withErrors(['otp' => 'OTP sudah kedaluwarsa pada ' . $matched->expires_at->format('d-m-Y H:i') . '. Silakan minta OTP baru.'])
                ->withInput();
        }

        if ($matched->created_at->lt(now()->subHours($maxHours))) {
            return back()->withErrors(['otp' => 'OTP lebih tua dari batas maksimal ' . $maxHours . ' jam. Silakan minta OTP baru.'])
                ->withInput();
        }

        // valid
        $matched->update(['used_at' => now()]);

        // Buka akses form Evaluasi Mitra (beda session key dengan kinerja)
        session(['evaluasi_mitra_allowed' => $rekap->id]);

        return redirect()->route('EvaluasiMitra.create', ['id' => $rekap->id]);
    }

    // app/Http/Controllers/EvaluasiMitraController.php

    public function kirimLinkDanOtp(Request $request, $rekapId)
    {
        $rekap = \App\Models\RekapKerjaSama::findOrFail($rekapId);

        $adminEmail = optional($request->user())->email
            ?? config('mail.admin_address')
            ?? config('mail.from.address');

        if (empty($adminEmail)) {
            return back()->with('error', 'Email admin tidak terkonfigurasi.');
        }

        // Generate OTP
        $plainOtp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // TTL & hard cap
        $ttlMinutes = (int) env('EVAL_KINERJA_OTP_TTL', 30);
        $maxHours   = (int) env('EVAL_KINERJA_OTP_MAX_HOURS', 12);

        $expiresAt = now()->addMinutes($ttlMinutes);
        $hardCap   = now()->addHours($maxHours);
        if ($expiresAt->greaterThan($hardCap)) $expiresAt = $hardCap;

        // Simpan OTP (pakai tabel OTP yang sama)
        $otp = new \App\Models\EvaluasiKinerjaOtp();
        $otp->rekap_id      = $rekap->id;
        $otp->code_hash     = \Hash::make($plainOtp);
        $otp->expires_at    = $expiresAt;
        $otp->used_at       = null;
        $otp->sent_to_email = $adminEmail;
        $otp->save();

        // ⬇️ Link untuk MITRA: KEPUASAN, bukan kinerja
        $tautanGate = route('EvaluasiMitra.otpGate', ['rekapId' => $rekap->id]);

        // Email ke MITRA: kirim link gate "Evaluasi Kepuasan Mitra"
        if (!empty($rekap->email_pihak_mitra)) {
            \Mail::to($rekap->email_pihak_mitra)
                ->send(new \App\Mail\MitraKepuasanLinkMail($rekap, $tautanGate));
        }

        // Email ke ADMIN: kirim OTP + link referensi
        \Mail::to($adminEmail)
            ->send(new \App\Mail\AdminOtpMail($rekap, $plainOtp, $tautanGate, 'kepuasan'));


        return back()->with('success', 'Tautan Evaluasi Kepuasan dikirim ke email mitra; OTP dikirim ke email admin.');
    }
}
