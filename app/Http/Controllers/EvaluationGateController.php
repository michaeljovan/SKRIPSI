<?php

// app/Http/Controllers/EvaluationGateController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekapKerjaSama;
use App\Models\EvaluasiLink;

class EvaluationGateController extends Controller
{
    public function show(RekapKerjaSama $rekap, string $token, Request $request)
    {
        // Cari token
        $hash = hash('sha256', $token);
        $link = EvaluasiLink::where('rekap_id', $rekap->id)
            ->where('token_hash', $hash)
            ->first();

        if (!$link) {
            abort(403, 'Tautan tidak valid.');
        }
        if (!$link->isUsable()) {
            abort(403, 'Tautan sudah tidak berlaku.');
        }

        // Tandai pakai (single-use)
        $link->update([
            'used_at'   => now(),
            'request_ip' => $request->ip(),
            'user_agent' => substr((string)$request->userAgent(), 0, 191),
        ]);

        // TODO: arahkan ke halaman/form evaluasi kamu
        // contoh: return redirect()->route('evaluasi.form', ['rekap' => $rekap->id, 'context' => $link->context]);
        return view('evaluasi.gate-ok', [
            'rekap'   => $rekap,
            'context' => $link->context,
        ]);
    }
}
