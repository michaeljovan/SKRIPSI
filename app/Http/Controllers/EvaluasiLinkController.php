<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\RekapKerjaSama;
use App\Models\EvaluasiLink;

class EvaluasiLinkController extends Controller
{
    /**
     * Landing dari email.
     * URL contoh: /evaluasi/link/{rekap}/{token}
     */
    public function show(Request $request, $rekap, $token)
    {
        $rekap = RekapKerjaSama::findOrFail($rekap);

        // Cari record EvaluasiLink yang cocok untuk rekap ini
        $link = $this->findLinkByToken((int) $rekap->id, (string) $token);

        if (!$link) {
            // Tidak ada link yang cocok
            return response()->view('errors.403', ['message' => 'Tautan tidak dikenali.'], 403);
        }

        // Cek status masa berlaku
        $expiresAt     = $link->expires_at ?? null;
        $usedAt        = $link->used_at ?? null;
        $invalidatedAt = $link->invalidated_at ?? null;

        // Pakai method model jika tersedia
        $isUsable = method_exists($link, 'isUsable')
            ? $link->isUsable()
            : (is_null($usedAt) && is_null($invalidatedAt) && (!$expiresAt instanceof Carbon || $expiresAt->isFuture()));

        // Simpan id link + token di session untuk proses berikutnya (opsional)
        session([
            'evaluasi_link_id' => $link->id,
            'evaluasi_link_token' => (string) $token, // untuk dibawa ke form berikutnya
        ]);

        // Tampilkan halaman pilihan jenis form (blade yang sudah kamu punya)
        // Di view, tombol akan tetap muncul, namun kalau $isUsable = false akan muncul alert + modal.
        return view('evaluasimitrapilihan', [
            'rekap'         => $rekap,
            'link'          => $link,
            'isUsable'      => (bool) $isUsable,
            'expiresAt'     => $expiresAt,
            'usedAt'        => $usedAt,
            'invalidatedAt' => $invalidatedAt,
        ]);
    }

    /**
     * Saat user memilih mode dari halaman pilihan.
     * URL contoh (GET): /evaluasi/link/start/{mode}?rekap=ID
     *
     * Disarankan pakai POST, tapi kita biarkan GET agar cepat integrasi.
     */
    public function start(Request $request, $mode)
    {
        $mode = in_array($mode, ['keseluruhan', 'perorangan']) ? $mode : 'keseluruhan';

        // Ambil dari session (di-set pada show)
        $linkId     = session('evaluasi_link_id');
        $plainToken = (string) session('evaluasi_link_token', '');
        $rekapId    = (int) $request->query('rekap');

        if (!$rekapId) {
            return redirect()->route('data_kerja_sama')->with('error', 'Rekap tidak valid.');
        }

        // Validasi link sekali lagi sebelum lanjut (defense-in-depth)
        $link = $linkId ? EvaluasiLink::find($linkId) : null;
        if (!$link || !$this->tokenMatches($link, $plainToken)) {
            return redirect()->route('EvaluasiMitra.pilihan', ['rekapId' => $rekapId])
                ->with('error', 'Tautan tidak valid.');
        }

        if (method_exists($link, 'isUsable') ? !$link->isUsable()
            : (!is_null($link->used_at) || !is_null($link->invalidated_at) || ($link->expires_at instanceof Carbon && $link->expires_at->isPast())))
        {
            return redirect()->route('EvaluasiMitra.pilihan', ['rekapId' => $rekapId])
                ->with('error', 'Tautan sudah tidak dapat digunakan (expired/used/invalid).');
        }

        // (Opsional) Tandai used saat mulai (atau bisa saat submit pertama form)
        $link->update(['used_at' => now()]);

        // Flag gate jika kamu butuh
        session(['evaluasi_mitra_kinerja_allowed' => $rekapId]);

        // Redirect ke form yang sudah ada di aplikasi kamu
        // Jika kamu juga butuh token diteruskan (mis. untuk verifikasi tambahan), tambahkan ->withQueryString atau param di route.
        return redirect()->route('EvaluasiMitraKinerja.create', [
            'id'   => $rekapId,
            'mode' => $mode,
            // 'token' => $plainToken, // kalau ingin diteruskan ke form
        ]);
    }

    /**
     * Mencocokkan token plain dengan token_hash di DB.
     * Mendukung 2 pola penyimpanan:
     * - bcrypt via Hash::make(...) → gunakan Hash::check
     * - sha256 hex string → bandingkan dengan hash('sha256', $plainToken)
     */
    protected function tokenMatches(EvaluasiLink $link, string $plainToken): bool
    {
        if (!$link || empty($link->token_hash) || $plainToken === '') {
            return false;
        }

        // 1) Coba cocokkan pakai Hash::check (bcrypt/argon)
        try {
            if (Hash::check($plainToken, $link->token_hash)) {
                return true;
            }
        } catch (\Throwable $e) {
            // lewat: kemungkinan token_hash bukan format bcrypt/argon
        }

        // 2) Fallback: cocokkan sha256 hex
        $sha256 = hash('sha256', $plainToken);
        return hash_equals($sha256, $link->token_hash);
    }

    /**
     * Cari EvaluasiLink berdasarkan rekap + token plain (mencoba bcrypt dan sha256).
     * Untuk efisiensi, batasi kandidat terakhir (mis. 50).
     */
    protected function findLinkByToken(int $rekapId, string $plainToken, int $limit = 50): ?EvaluasiLink
    {
        if ($plainToken === '') {
            return null;
        }

        $cands = EvaluasiLink::where('rekap_id', $rekapId)
            ->latest('id')
            ->take($limit)
            ->get();

        foreach ($cands as $cand) {
            if ($this->tokenMatches($cand, $plainToken)) {
                return $cand;
            }
        }
        return null;
    }
}
