<?php

namespace App\Http\Controllers;

use App\Models\RekapKerjaSama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RekapKerjaSamaController extends Controller
{
    public function index()
    {
        $rekapKerjaSama = RekapKerjaSama::orderBy('created_at', 'desc')->get();
        return view('datadokumenkerjasama', compact('rekapKerjaSama'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'noDokumen' => 'required|unique:rekapkerjasama,no_dokumen',
            'unit' => 'required',
            'mitraKerjaSama' => 'required',
            'judulKerjaSama' => 'required',
            'bentukKerjaSama' => 'required|array',
            'pihakUKDW' => 'required',
            'pihakMitra' => 'required',
            'tanggalMulai' => 'required|date',
            'tanggalSelesai' => 'required|date|after_or_equal:tanggalMulai',
            'kategori' => 'required',
            'dokumenPendukung' => 'required|file|mimes:pdf|max:5120',
        ]);

        // Calculate duration
        $startDate = new \DateTime($request->tanggalMulai);
        $endDate = new \DateTime($request->tanggalSelesai);
        $duration = $endDate->diff($startDate)->days + 1;

        // Handle file upload
        if ($request->hasFile('dokumenPendukung')) {
            $file = $request->file('dokumenPendukung');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('dokumen_kerja_sama', $fileName, 'public');
        }

        // Create new record
        RekapKerjaSama::create([
            'no_dokumen' => $request->noDokumen,
            'unit' => $request->unit,
            'mitra_kerja_sama' => $request->mitraKerjaSama,
            'judul_kerja_sama' => $request->judulKerjaSama,
            'bentuk_kerja_sama' => $request->bentukKerjaSama,
            'bentuk_kerja_sama_text' => $request->bentukKerjaSamaText,
            'pihak_ukdw' => $request->pihakUKDW,
            'pihak_mitra' => $request->pihakMitra,
            'tanggal_mulai' => $request->tanggalMulai,
            'tanggal_selesai' => $request->tanggalSelesai,
            'masa_berlaku' => $duration,
            'kategori' => $request->kategori,
            'in_kind' => $request->inKind,
            'total_in_kind' => $request->totalInKind ? str_replace(',', '', $request->totalInKind) : null,
            'in_cash' => $request->inCash ? str_replace(',', '', $request->inCash) : null,
            'total_in_cash' => $request->totalInCash ? str_replace(',', '', $request->totalInCash) : null,
            'jumlah_implementasi' => $request->jumlahImplementasi,
            'dokumen_path' => $filePath,
        ]);
        return redirect()->back()->with('success', 'Data kerja sama berhasil disimpan!');
    }

    public function delete($id)
    {
        try {
            // This check is redundant since the route is already protected by auth middleware
            $rekap = RekapKerjaSama::findOrFail($id);

            // Delete the file from storage if exists
            if ($rekap->dokumen_path) {
                Storage::disk('public')->delete($rekap->dokumen_path);
            }

            $rekap->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus!'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan!'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function create()
    {
        return view('inputlaporanpelaksanaankerjasama');
    }
}
