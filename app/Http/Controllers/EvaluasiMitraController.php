<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiMitra;
use App\Models\RekapKerjaSama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Mail\MitraEvaluasiLinkMail;
use Carbon\Carbon;
use Throwable;
use App\Models\EvaluasiMitraPerorangan;

class EvaluasiMitraController extends Controller
{
    /** List evaluasi mitra (admin) */
    public function index()
    {
        // gunakan page name berbeda supaya paginasi masing2 tab tidak saling bentrok
        $mitraKes = EvaluasiMitra::with('rekapKerjasama.laporanPelaksanaan')
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'kes_page');

        $mitraPer = EvaluasiMitraPerorangan::with('rekapKerjasama.laporanPelaksanaan')
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'per_page');

        return view('evaluasikerjasamamitra', [
            'evaluasimitra' => $mitraKes, // keseluruhan (nama lama dipertahankan)
            'evaluasiPerorangan' => $mitraPer,
        ]);
    }

    /** Form input evaluasi MITRA (keseluruhan) */
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
        $dosenCount     = $laporan->jumlah_dosen_terlibat     ?? count($dosenList);
        $mahasiswaCount = $laporan->jumlah_mahasiswa_terlibat ?? count($mahasiswaList);

        return view('inputevaluasikerjasamamitra', compact(
            'rekap',
            'laporan',
            'dosenList',
            'mahasiswaList',
            'dosenCount',
            'mahasiswaCount'
        ));
    }

    /** Simpan evaluasi MITRA (keseluruhan) */
    public function store(Request $request)
    {
        $map = [
            'Sangat Tinggi' => 5,
            'Tinggi'        => 4,
            'Cukup'         => 3,
            'Kurang'        => 2,
            'Sangat Kurang' => 1,
        ];

        $validated = $request->validate([
            'rekap_id'      => 'required|exists:rekapkerjasama,id',
            'nodok'         => 'required|string|max:255',
            'mitra'         => 'required|string|max:255',
            'pengisi_mitra' => 'required|string|max:100',

            'integritas'        => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'keahlian'          => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'komunikasi'        => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'kerjasamatim'      => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'pengembangandiri'  => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'kreativitas'       => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'bahasaasing'       => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',

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
                'bahasaasing'
            ] as $f
        ) {
            $validated[$f] = $map[$validated[$f]];
        }

        if ($request->hasFile('pdfFile')) {
            $validated['file_pdf'] = $request->file('pdfFile')->store('evaluasi_pdf', 'public');
        }

        EvaluasiMitra::create($validated);

        // tandai rekap sudah punya evaluasi mitra
        RekapKerjaSama::where('id', $validated['rekap_id'])->update(['is_mitra' => true]);

        return back()->with('success', 'Evaluasi berhasil disimpan');
    }

    /** Hapus evaluasi MITRA (admin) */
    public function delete($id)
    {
        try {
            if (!is_numeric($id)) {
                return response()->json(['success' => false, 'message' => 'ID tidak valid'], 400);
            }

            $evaluasi = EvaluasiMitra::with('rekapKerjasama')
                ->where('idmitra', $id)
                ->first();

            if (!$evaluasi) {
                return response()->json(['success' => false, 'message' => 'Data evaluasi mitra tidak ditemukan'], 404);
            }

            $rekapId = $evaluasi->rekap_id;

            if ($evaluasi->file_pdf && Storage::disk('public')->exists($evaluasi->file_pdf)) {
                Storage::disk('public')->delete($evaluasi->file_pdf);
            }

            $evaluasi->delete();

            // set is_mitra=false jika benar2 tidak ada evaluasi lain
            $masihAda = EvaluasiMitra::where('rekap_id', $rekapId)->exists();
            if (!$masihAda) {
                RekapKerjaSama::where('id', $rekapId)->update(['is_mitra' => false]);
            }

            return response()->json(['success' => true, 'message' => 'Evaluasi mitra berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus evaluasi mitra: ' . $e->getMessage()], 500);
        }
    }

    /** Form edit evaluasi MITRA (admin) */
    public function edit($id)
    {
        $evaluasi = EvaluasiMitra::findOrFail($id);
        $rekap    = RekapKerjaSama::findOrFail($evaluasi->rekap_id);

        return view('evaluasikerjasamamitraedit', compact('evaluasi', 'rekap'));
    }

    /** Update evaluasi MITRA (admin) */
    public function update(Request $request, $id)
    {
        $evaluasi = EvaluasiMitra::where('idmitra', $id)->firstOrFail();

        $validated = $request->validate([
            'integritas'       => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'keahlian'         => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'komunikasi'       => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'kerjasamatim'     => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'pengembangandiri' => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'kreativitas'      => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'bahasaasing'      => 'required|in:Sangat Tinggi,Tinggi,Cukup,Kurang,Sangat Kurang',
            'pdfFile'          => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $map = [
            'Sangat Tinggi' => 5,
            'Tinggi'        => 4,
            'Cukup'         => 3,
            'Kurang'        => 2,
            'Sangat Kurang' => 1,
        ];

        if ($request->hasFile('pdfFile')) {
            if ($evaluasi->file_pdf && Storage::exists('public/' . $evaluasi->file_pdf)) {
                Storage::delete('public/' . $evaluasi->file_pdf);
            }
            $file     = $request->file('pdfFile');
            $filename = 'eval_mitra_' . time() . '.' . $file->getClientOriginalExtension();
            $path     = $file->storeAs('public/evaluasi_mitra', $filename);
            $validated['file_pdf'] = str_replace('public/', '', $path);
        }

        foreach (
            [
                'integritas',
                'keahlian',
                'komunikasi',
                'kerjasamatim',
                'pengembangandiri',
                'kreativitas',
                'bahasaasing'
            ] as $f
        ) {
            $validated[$f] = $map[$validated[$f]] ?? null;
        }

        $evaluasi->update($validated);

        return redirect()->route('EvaluasiMitra.index')
            ->with('success', 'Evaluasi mitra berhasil diperbarui');
    }

    /**
     * KIRIM LINK (tanpa OTP) ke email mitra → menuju halaman pilihan (Keseluruhan / Perorangan).
     */
    public function kirimLink(Request $request, $rekapId)
    {
        $rekap = RekapKerjaSama::findOrFail($rekapId);

        // Ambil email dari input → fallback ke model
        $toEmail = trim((string) $request->input('email_mitra', $rekap->email_mitra ?? $rekap->email_pihak_mitra ?? ''));
        $request->merge(['email_mitra' => $toEmail]);

        $request->validate([
            'email_mitra' => 'required|email:rfc,dns',
        ], [
            'email_mitra.required' => 'Email mitra belum diisi.',
            'email_mitra.email'    => 'Format email mitra tidak valid.',
        ]);

        // Info masa berlaku (hanya ditampilkan di email)
        $expiresAt = Carbon::now()->addHours(24);

        // Link ke halaman pilihan (tanpa token/OTP)
        $url = Route::has('EvaluasiMitra.pilihan')
            ? route('EvaluasiMitra.pilihan', ['rekapId' => $rekap->id])
            : url("evaluasi-mitra/{$rekap->id}/pilihan");

        try {
            Mail::to($toEmail)->send(new MitraEvaluasiLinkMail($rekap, $url, $expiresAt, 'kepuasan'));
        } catch (Throwable $e) {
            return back()->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }

        return back()->with(
            'success',
            'Tautan evaluasi mitra dikirim ke ' . $toEmail . ' (berlaku s.d. ' .
                $expiresAt->timezone('Asia/Jakarta')->format('d/m/Y H:i') . ' WIB).'
        );
    }

    /** Halaman pilihan (Keseluruhan / Perorangan) */
    public function pilihanForm($rekapId)
    {
        $rekap = RekapKerjaSama::findOrFail($rekapId);
        return view('evaluasimitrapilihan', compact('rekap'));
    }
}
