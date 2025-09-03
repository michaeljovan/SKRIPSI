<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekapKerjaSama;
use App\Models\EvaluasiLink; // opsional jika kamu pakai DB token
use Carbon\Carbon;

class EvaluasiLinkController extends Controller
{
    /**
     * Halaman landing saat klik link dari email.
     * URL: /evaluasi/link/{rekap}/{token} (signed)
     */
    public function show(Request $request, $rekap, $token)
    {
        $rekap = RekapKerjaSama::findOrFail($rekap);

        // OPSIONAL: validasi ke tabel evaluasi_links untuk single-use/revoke
        if (class_exists(\App\Models\EvaluasiLink::class)) {
            $hash = hash('sha256', $token);

            $record = EvaluasiLink::where('rekap_id', $rekap->id)
                ->where('token_hash', $hash)
                ->first();

            if (!$record) {
                return response()->view('errors.403', ['message' => 'Tautan tidak dikenali.'], 403);
            }
            if ($record->invalidated_at) {
                return response()->view('errors.403', ['message' => 'Tautan ini telah dibatalkan.'], 403);
            }
            if ($record->expires_at && $record->expires_at->isPast()) {
                return response()->view('errors.403', ['message' => 'Tautan sudah kedaluwarsa.'], 403);
            }

            // simpan id link ke session (kalau mau ditandai used saat "start")
            session(['evaluasi_link_id' => $record->id]);
        }

        // Tampilkan halaman pilihan mode
        return view('evaluasi_pilih_mode', [
            'rekap'   => $rekap,
            'context' => $request->query('ctx', 'kinerja'),
        ]);
    }

    /**
     * Saat user pilih mode di landing.
     * URL: /evaluasi/link/start/{mode}
     */
    public function start(Request $request, $mode)
    {
        $mode = in_array($mode, ['keseluruhan', 'perorangan']) ? $mode : 'keseluruhan';

        // Kalau kamu ingin tandai link digunakan (single-use) lakukan di sini
        if (class_exists(\App\Models\EvaluasiLink::class)) {
            if ($id = session('evaluasi_link_id')) {
                \App\Models\EvaluasiLink::whereKey($id)->update(['used_at' => now()]);
                // session()->forget('evaluasi_link_id'); // boleh dihapus jika mau satu kali saja
            }
        }

        // izinkan akses form evaluasi existing
        $rekapId = (int) request()->query('rekap'); // kita kirim melalui link from landing
        if (!$rekapId) {
            return redirect()->route('home')->with('error', 'Rekap tidak valid.');
        }

        // Set flag yang saat ini dipakai gate di EvaluasiMitraKinerjaController@create
        session(['evaluasi_mitra_kinerja_allowed' => $rekapId]);

        // Redirect ke form existing kamu + bawa mode (opsional disesuaikan di blade form)
        return redirect()->route('EvaluasiMitraKinerja.create', ['id' => $rekapId, 'mode' => $mode]);
    }
}
