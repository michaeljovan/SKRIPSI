<?php

namespace App\Services;

use App\Models\RekapKerjaSama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use DateTime;

class RekapKerjaSamaService
{
    public function noDokumenExists($noDokumen)
    {
        return RekapKerjaSama::where('no_dokumen', $noDokumen)->exists();
    }

    public function store(Request $request)
    {
        $request->merge([
            'parent_id' => $request->parent_id === 'none' ? null : $request->parent_id
        ]);

        $validated = $request->validate([
            'noDokumen' => 'required|unique:rekapkerjasama,no_dokumen',
            'unit' => 'required',
            'mitraKerjaSama' => 'required',
            'judulKerjaSama' => 'required',
            'bentukKerjaSama' => 'required|array|min:1',
            'bentukKerjaSama.*' => 'string|in:Penelitian,Pendidikan,Pengabdian',
            'jenisKerjaSama' => 'required|string|in:MoU,MoA,IA',
            'pihakUKDW' => 'required',
            'pihakMitra' => 'required',
            'tanggalMulai' => 'required|date|before_or_equal:tanggalSelesai',
            'tanggalSelesai' => 'required|date|after_or_equal:tanggalMulai',
            'kategori' => 'required|string|in:nasional,internasional',
            'inKind' => 'nullable|numeric',
            'totalInKind' => 'nullable|numeric',
            'inCash' => 'nullable|numeric',
            'totalInCash' => 'nullable|numeric',
            'jumlahImplementasi' => 'nullable|integer|min:0',
            'dokumenPendukung' => 'required|file|mimes:pdf|max:5120',
            'parent_id' => 'nullable|exists:rekapkerjasama,id',
        ], [
            'bentukKerjaSama.min' => 'Pilih minimal satu bentuk kerja sama.',
            'tanggalSelesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
            'totalInKind.numeric' => 'In Kind dan In Cash harus berupa angka.',
            'inCash.numeric' => 'In Cash harus berupa angka.',
            'totalInCash.numeric' => 'Total In Cash harus berupa angka.',
            'dokumenPendukung.max' => 'Ukuran dokumen maksimal 5MB.',
        ]);

        $duration = (new DateTime($request->tanggalMulai))->diff(new DateTime($request->tanggalSelesai))->days + 1;
        $filePath = $request->file('dokumenPendukung')->store('dokumen_kerja_sama', 'public');

        $noInduk = optional(RekapKerjaSama::find($request->parent_id))->no_dokumen;

        RekapKerjaSama::create([
            'no_dokumen' => $request->noDokumen,
            'unit' => $request->unit,
            'mitra_kerja_sama' => $request->mitraKerjaSama,
            'judul_kerja_sama' => $request->judulKerjaSama,
            'bentuk_kerja_sama' => implode(', ', $validated['bentukKerjaSama']),
            'jenis_kerja_sama' => $request->jenisKerjaSama,
            'pihak_ukdw' => $request->pihakUKDW,
            'pihak_mitra' => $request->pihakMitra,
            'tanggal_mulai' => $request->tanggalMulai,
            'tanggal_selesai' => $request->tanggalSelesai,
            'masa_berlaku' => $duration,
            'kategori' => $request->kategori,
            'in_kind' => str_replace(',', '', $request->totalInKind),
            'total_in_kind' => str_replace(',', '', $request->totalInKind),
            'in_cash' => str_replace(',', '', $request->inCash),
            'total_in_cash' => str_replace(',', '', $request->totalInCash),
            'jumlah_implementasi' => $request->jumlahImplementasi ?? 0,
            'dokumen_path' => $filePath,
            'parent_id' => $request->parent_id,
            'no_dokumen_induk' => $noInduk,
        ]);

        return [
            'success' => true,
            'message' => 'Data kerja sama berhasil disimpan!',
            'redirect' => route('data_kerja_sama')
        ];
    }

    public function delete($id)
    {
        $rekap = RekapKerjaSama::findOrFail($id);
        if ($rekap->dokumen_path) {
            Storage::disk('public')->delete($rekap->dokumen_path);
        }
        $rekap->delete();

        return ['success' => true, 'message' => 'Data berhasil dihapus!'];
    }

    public function storeData(Request $request)
    {
        $request->merge(['parent_id' => $request->parent_id === 'none' ? null : $request->parent_id]);

        $validated = $request->validate([
            'noDokumen' => 'required|unique:rekapkerjasama,no_dokumen',
            // ... validasi lain seperti di controller ...
        ]);

        $duration = (new \DateTime($request->tanggalMulai))->diff(new \DateTime($request->tanggalSelesai))->days + 1;
        $filePath = $request->file('dokumenPendukung')->store('dokumen_kerja_sama', 'public');

        $noInduk = optional(RekapKerjaSama::find($request->parent_id))->no_dokumen;

        RekapKerjaSama::create([
            'no_dokumen' => $request->noDokumen,
            // ... sisanya ...
            'no_dokumen_induk' => $noInduk,
        ]);

        return [
            'success' => true,
            'message' => 'Data kerja sama berhasil disimpan!',
            'redirect' => route('data_kerja_sama')
        ];
    }

    public function deleteData($id)
    {
        $rekap = RekapKerjaSama::findOrFail($id);
        if ($rekap->dokumen_path) Storage::disk('public')->delete($rekap->dokumen_path);
        $rekap->delete();

        return ['success' => true, 'message' => 'Data berhasil dihapus!'];
    }
}
