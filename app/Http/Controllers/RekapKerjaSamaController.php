<?php

namespace App\Http\Controllers;

use App\Models\RekapKerjaSama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RekapKerjaSamaController extends Controller
{
    public function index(Request $request)
    {
        $query = RekapKerjaSama::query()->orderBy('created_at', 'desc');

        // Filter No Dokumen
        if ($request->filled('no_dokumen')) {
            $query->where('no_dokumen', 'like', '%' . $request->no_dokumen . '%');
        }

        // Filter Unit
        if ($request->filled('unit')) {
            $query->where('unit', $request->unit);
        }

        // Filter Mitra
        if ($request->filled('mitra')) {
            $query->where('mitra_kerja_sama', 'like', '%' . $request->mitra . '%');
        }

        // Filter Kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter Judul
        if ($request->filled('judul')) {
            $query->where('judul_kerja_sama', 'like', '%' . $request->judul . '%');
        }

        // Filter Jenis Kerja Sama
        if ($request->filled('jenis_kerja_sama')) {
            $query->where('jenis_kerja_sama', $request->jenis_kerja_sama);
        }

        // Filter Status Laporan
        if ($request->filled('is_laporan')) {
            $query->where('is_laporan', $request->is_laporan);
        }

        // Filter Tanggal Mulai
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_mulai', '>=', $request->tanggal_mulai);
        }

        // Filter Tanggal Selesai
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal_selesai', '<=', $request->tanggal_selesai);
        }

        $rekapKerjaSama = $query->get();

        return view('datadokumenkerjasama', compact('rekapKerjaSama'));
    }


    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'noDokumen' => 'required|unique:rekapkerjasama,no_dokumen',
                'unit' => 'required',
                'mitraKerjaSama' => 'required',
                'judulKerjaSama' => 'required',
                'bentukKerjaSama' => 'required|array|min:1',
                'bentukKerjaSama.*' => 'string|in:Penelitian,Pendidikan,Pengabdian',
                'jenisKerjaSama' => 'required',
                'pihakUKDW' => 'required',
                'pihakMitra' => 'required',
                'tanggalMulai' => 'required|date|before_or_equal:tanggalSelesai',
                'tanggalSelesai' => 'required|date|after_or_equal:tanggalMulai',
                'kategori' => 'required|string|in:nasional,internasional',
                'dokumenPendukung' => 'required|file|mimes:pdf|max:5120',
            ], [
                'bentukKerjaSama.min' => 'Pilih minimal satu bentuk kerja sama',
                'tanggalSelesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai',
                'dokumenPendukung.max' => 'Ukuran dokumen maksimal 5MB',
            ]);

            // Calculate duration
            $startDate = new \DateTime($request->tanggalMulai);
            $endDate = new \DateTime($request->tanggalSelesai);
            $duration = $endDate->diff($startDate)->days + 1;

            // Handle file upload
            if (!$request->hasFile('dokumenPendukung')) {
                throw new \Exception('Dokumen pendukung harus diupload');
            }

            $file = $request->file('dokumenPendukung');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('dokumen_kerja_sama', $fileName, 'public');


            // Create new record
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
                'in_kind' => $request->inKind,
                'total_in_kind' => $request->totalInKind ? str_replace(',', '', $request->totalInKind) : null,
                'in_cash' => $request->inCash ? str_replace(',', '', $request->inCash) : null,
                'total_in_cash' => $request->totalInCash ? str_replace(',', '', $request->totalInCash) : null,
                'jumlah_implementasi' => $request->jumlahImplementasi,
                'dokumen_path' => $filePath,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data kerja sama berhasil disimpan!',
                'redirect' => route('data_kerja_sama')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
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
        return view('inputrekapkerjasama');
    }

    public function edit($id)
    {
        $rekap = RekapKerjaSama::findOrFail($id);
        return view('editrekapkerjasama', compact('rekap'));
    }

    public function update(Request $request, $id)
    {
        try {
            $rekap = RekapKerjaSama::findOrFail($id);

            $validated = $request->validate([
                'noDokumen' => 'required|unique:rekapkerjasama,no_dokumen,' . $id,
                'unit' => 'required',
                'mitraKerjaSama' => 'required',
                'judulKerjaSama' => 'required',
                'bentukKerjaSama' => 'required|array',
                'bentukKerjaSama.*' => 'string|in:Penelitian,Pendidikan,Pengabdian',
                'jenisKerjaSama' => 'required|in:MoU,MoA,IA',
                'pihakUKDW' => 'required',
                'pihakMitra' => 'required',
                'tanggalMulai' => 'required|date',
                'tanggalSelesai' => 'required|date|after_or_equal:tanggalMulai',
                'kategori' => 'required|in:nasional,internasional',
                'dokumenPendukung' => 'nullable|file|mimes:pdf|max:5120',
            ]);

            // Calculate duration
            $startDate = new \DateTime($request->tanggalMulai);
            $endDate = new \DateTime($request->tanggalSelesai);
            $duration = $endDate->diff($startDate)->days + 1;

            // Handle file upload if new file is provided
            $filePath = $rekap->dokumen_path;
            if ($request->hasFile('dokumenPendukung')) {
                // Delete old file if exists
                if ($rekap->dokumen_path) {
                    Storage::disk('public')->delete($rekap->dokumen_path);
                }

                // Store new file
                $file = $request->file('dokumenPendukung');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('dokumen_kerja_sama', $fileName, 'public');
            }

            // Convert bentukKerjaSama array to string if needed
            $bentukKerjaSama = is_array($request->bentukKerjaSama)
                ? implode(', ', $request->bentukKerjaSama)
                : $request->bentukKerjaSama;

            // Update record
            $rekap->update([
                'no_dokumen' => $request->noDokumen,
                'unit' => $request->unit,
                'mitra_kerja_sama' => $request->mitraKerjaSama,
                'judul_kerja_sama' => $request->judulKerjaSama,
                'bentuk_kerja_sama' => $bentukKerjaSama,
                'jenis_kerja_sama' => $request->jenisKerjaSama,
                'pihak_ukdw' => $request->pihakUKDW,
                'pihak_mitra' => $request->pihakMitra,
                'tanggal_mulai' => $request->tanggalMulai,
                'tanggal_selesai' => $request->tanggalSelesai,
                'masa_berlaku' => $duration,
                'kategori' => $request->kategori,
                'in_kind' => $request->inKind,
                'total_in_kind' => $request->totalInKind ? str_replace(['.', ','], '', $request->totalInKind) : null,
                'in_cash' => $request->inCash ? str_replace(['.', ','], '', $request->inCash) : null,
                'total_in_cash' => $request->totalInCash ? str_replace(['.', ','], '', $request->totalInCash) : null,
                'jumlah_implementasi' => $request->jumlahImplementasi,
                'dokumen_path' => $filePath,
            ]);

            // Return JSON response for AJAX requests
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data kerja sama berhasil diperbarui!',
                    'redirect' => route('data_kerja_sama')
                ]);
            }

            return redirect()->route('data_kerja_sama')->with('success', 'Data kerja sama berhasil diperbarui!');
        } catch (\Exception $e) {
            // Return JSON error for AJAX requests
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => $e instanceof \Illuminate\Validation\ValidationException
                        ? $e->errors()
                        : []
                ], 500);
            }

            return back()->withErrors($e->getMessage());
        }
    }
}
