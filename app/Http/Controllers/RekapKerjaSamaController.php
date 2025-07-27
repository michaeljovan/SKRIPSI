<?php

namespace App\Http\Controllers;

use App\Models\RekapKerjaSama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RekapKerjaSamaController extends Controller
{
    public function index(Request $request)
    {
        $query = RekapKerjaSama::with('induk')->orderBy('created_at', 'desc');

        if ($request->filled('no_dokumen')) {
            $query->where('no_dokumen', 'like', '%' . $request->no_dokumen . '%');
        }

        if ($request->filled('unit')) {
            $query->where('unit', $request->unit);
        }

        if ($request->filled('mitra')) {
            $query->where('mitra_kerja_sama', 'like', '%' . $request->mitra . '%');
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('judul')) {
            $query->where('judul_kerja_sama', 'like', '%' . $request->judul . '%');
        }

        if ($request->filled('jenis_kerja_sama')) {
            $query->where('jenis_kerja_sama', $request->jenis_kerja_sama);
        }

        if ($request->filled('is_laporan')) {
            $query->where('is_laporan', $request->is_laporan);
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_mulai', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal_selesai', '<=', $request->tanggal_selesai);
        }


        if ($request->has('bentuk_kerja_sama')) {
            foreach ((array) $request->bentuk_kerja_sama as $bentuk) {
                $query->where('bentuk_kerja_sama', 'LIKE', '%' . trim($bentuk) . '%');
            }
        }

        $rekapKerjaSama = $query->get();

        return view('datadokumenkerjasama', compact('rekapKerjaSama'));
    }

    public function cekNoDokumen(Request $request)
    {
        $exists = RekapKerjaSama::where('no_dokumen', $request->no_dokumen)->exists();
        return response()->json(['exists' => $exists]);
    }


    public function store(Request $request)
    {
        try {
            if ($request->parent_id === 'none') {
                $request->merge(['parent_id' => null]);
            }

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
                'totalInKind.numeric' => 'In Kind dan In Cash  harus berupa angka.',
                'inCash.numeric' => 'In Cash harus berupa angka.',
                'totalInCash.numeric' => 'Total In Cash harus berupa angka.',
                'dokumenPendukung.max' => 'Ukuran dokumen maksimal 5MB.',
            ]);

            // Hitung masa berlaku
            $startDate = new \DateTime($request->tanggalMulai);
            $endDate = new \DateTime($request->tanggalSelesai);
            $duration = $endDate->diff($startDate)->days + 1;

            // Upload file
            if (!$request->hasFile('dokumenPendukung')) {
                throw new \Exception('Dokumen pendukung harus diupload');
            }

            $file = $request->file('dokumenPendukung');
            $filePath = $file->store('dokumen_kerja_sama', 'public');

            // Ambil informasi no dokumen induk (jika ada)
            $parentId = $request->parent_id !== 'none' ? $request->parent_id : null;
            $noInduk = null;

            if ($parentId) {
                $induk = RekapKerjaSama::find($parentId);
                if ($induk) {
                    $noInduk = $induk->no_dokumen;
                }
            }

            // Simpan ke DB
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
                'in_kind' => $request->totalInKind ? str_replace(',', '', $request->totalInKind) : null,
                'total_in_kind' => $request->totalInKind ? str_replace(',', '', $request->totalInKind) : null,
                'in_cash' => $request->inCash ? str_replace(',', '', $request->inCash) : null,
                'total_in_cash' => $request->totalInCash ? str_replace(',', '', $request->totalInCash) : null,
                'jumlah_implementasi' => $request->jumlahImplementasi ?? 0,
                'dokumen_path' => $filePath,
                'parent_id' => $parentId,
                'no_dokumen_induk' => $noInduk,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data kerja sama berhasil disimpan!',
                'redirect' => route('data_kerja_sama')
            ]);
        } catch (ValidationException $e) {
            // Kembalikan response 422 agar sesuai dengan test
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getDokumenInduk(Request $request)
    {
        $jenis = $request->input('jenis');

        if (!in_array($jenis, ['MoU', 'MoA', 'IA'])) {
            return response()->json([], 400);
        }

        $allowed = match ($jenis) {
            'MoA' => ['MoU'],
            'IA' => ['MoU', 'MoA'],
            default => [],
        };

        $dokumen = RekapKerjaSama::whereIn('jenis_kerja_sama', $allowed)
            ->select('id', 'no_dokumen', 'judul_kerja_sama', 'mitra_kerja_sama')
            ->orderBy('created_at', 'desc')
            ->get();

        // Tambahkan pilihan opsional untuk "Tidak Ada Induk"
        $dokumen->prepend((object)[
            'id' => 'none',
            'no_dokumen' => 'Tidak Ada Induk',
            'judul_kerja_sama' => '-',
            'mitra_kerja_sama' => '-',
        ]);

        return response()->json($dokumen);
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

        // Ambil dokumen induk yang sesuai jenisnya, hindari dirinya sendiri
        $dokumenInduk = collect(); // default kosong

        if ($rekap->jenis_kerja_sama === 'MoA') {
            $dokumenInduk = RekapKerjaSama::where('jenis_kerja_sama', 'MoU')
                ->where('id', '!=', $rekap->id)
                ->get();
        } elseif ($rekap->jenis_kerja_sama === 'IA') {
            $dokumenInduk = RekapKerjaSama::whereIn('jenis_kerja_sama', ['MoU', 'MoA'])
                ->where('id', '!=', $rekap->id)
                ->get();
        }

        return view('editrekapkerjasama', compact('rekap', 'dokumenInduk'));
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
                'parent_id' => 'nullable|exists:rekapkerjasama,id'
            ]);

            // Hitung masa berlaku
            $duration = (new \DateTime($request->tanggalMulai))->diff(new \DateTime($request->tanggalSelesai))->days + 1;

            // Handle file upload
            $filePath = $rekap->dokumen_path;
            if ($request->hasFile('dokumenPendukung')) {
                if ($rekap->dokumen_path && Storage::disk('public')->exists($rekap->dokumen_path)) {
                    Storage::disk('public')->delete($rekap->dokumen_path);
                }
                $filePath = $request->file('dokumenPendukung')->store('dokumen_kerja_sama', 'public');
            }

            // Ambil dokumen induk (jika ada) untuk menyimpan nomor dokumen induknya
            $parentId = $request->parent_id;
            $noDokInduk = null;

            if ($parentId) {
                $parent = RekapKerjaSama::find($parentId);
                $noDokInduk = $parent ? $parent->no_dokumen : null;
            }

            // Simpan data
            $rekap->update([
                'no_dokumen' => $request->noDokumen,
                'unit' => $request->unit,
                'mitra_kerja_sama' => $request->mitraKerjaSama,
                'judul_kerja_sama' => $request->judulKerjaSama,
                'bentuk_kerja_sama' => implode(', ', $request->bentukKerjaSama),
                'jenis_kerja_sama' => $request->jenisKerjaSama,
                'pihak_ukdw' => $request->pihakUKDW,
                'pihak_mitra' => $request->pihakMitra,
                'tanggal_mulai' => $request->tanggalMulai,
                'tanggal_selesai' => $request->tanggalSelesai,
                'masa_berlaku' => $duration,
                'kategori' => $request->kategori,
                'in_kind' => $request->in_kind ? str_replace(['.', ','], '', $request->in_kind) : null,
                'total_in_kind' => $request->totalInKind ? str_replace(['.', ','], '', $request->totalInKind) : null,
                'in_cash' => $request->inCash ? str_replace(['.', ','], '', $request->inCash) : null,
                'total_in_cash' => $request->totalInCash ? str_replace(['.', ','], '', $request->totalInCash) : null,
                'jumlah_implementasi' => $request->jumlahImplementasi,
                'dokumen_path' => $filePath,
                'parent_id' => $parentId,
                'no_dokumen_induk' => $noDokInduk,
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data kerja sama berhasil diperbarui!',
                    'redirect' => route('data_kerja_sama')
                ]);
            }

            return redirect()->route('data_kerja_sama')->with('success', 'Data kerja sama berhasil diperbarui!');
        } catch (\Exception $e) {
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

    public function lihatPDF($id)
    {
        $rekap = RekapKerjaSama::findOrFail($id);
        $disk = Storage::disk('public');

        if (!$disk->exists($rekap->dokumen_path)) {
            abort(404);
        }

        return response($disk->get($rekap->dokumen_path), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
