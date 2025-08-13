<?php

namespace App\Http\Controllers;

use App\Models\RekapKerjaSama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Services\RekapKerjaSamaService;
use Tests\TestCase;
use Mockery;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;


class RekapKerjaSamaController extends Controller
{
    protected $service;

    public function __construct(RekapKerjaSamaService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $query = RekapKerjaSama::with('induk')->orderByDesc('created_at');

        $filters = [
            'no_dokumen' => 'like',
            'unit' => '=',
            'mitra_kerja_sama' => 'like',
            'kategori' => '=',
            'judul_kerja_sama' => 'like',
            'jenis_kerja_sama' => '=',
            'is_laporan' => '=',
        ];

        foreach ($filters as $field => $operator) {
            if ($request->filled($field)) {
                $value = $operator === 'like' ? '%' . $request->$field . '%' : $request->$field;
                $query->where($field, $operator, $value);
            }
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
        if ($request->filled('mitra')) {
            $query->where('mitra_kerja_sama', 'like', '%' . $request->mitra . '%');
        }

        if ($request->filled('judul')) {
            $query->where('judul_kerja_sama', 'like', '%' . $request->judul . '%');
        }

        return view('datadokumenkerjasama', ['rekapKerjaSama' => $query->get()]);
    }

    public function cekNoDokumen(Request $request)
    {
        return response()->json(['exists' => $this->service->noDokumenExists($request->no_dokumen)]);
    }

    public function store(Request $request)
    {
        try {
            $request->merge(['parent_id' => $request->parent_id === 'none' ? null : $request->parent_id]);

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
                'emailMitra' => 'required',
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

            $duration = (new \DateTime($request->tanggalMulai))->diff(new \DateTime($request->tanggalSelesai))->days + 1;
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
                'email_pihak_mitra' => $request->emailMitra,
                'tanggal_mulai' => $request->tanggalMulai,
                'tanggal_selesai' => $request->tanggalSelesai,
                'masa_berlaku' => $duration,
                'kategori' => $request->kategori,
                'in_kind' => str_replace(',', '', $request->totalInKind) ?: 0,
                'total_in_kind' => str_replace(',', '', $request->totalInKind) ?: 0,
                'in_cash' => str_replace(',', '', $request->inCash) ?: 0,
                'total_in_cash' => str_replace(',', '', $request->totalInCash) ?: 0,
                'jumlah_implementasi' => $request->jumlahImplementasi ?? 0,
                'dokumen_path' => $filePath,
                'parent_id' => $request->parent_id,
                'no_dokumen_induk' => $noInduk,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data kerja sama berhasil disimpan!',
                'redirect' => route('data_kerja_sama')
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            $rekap = RekapKerjaSama::findOrFail($id);
            if ($rekap->dokumen_path) Storage::disk('public')->delete($rekap->dokumen_path);
            $rekap->delete();

            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus!']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan!'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data: ' . $e->getMessage()], 500);
        }
    }

    public function getDokumenInduk(Request $request)
    {
        $jenis = $request->input('jenis');

        // Validasi jenis
        if (!in_array($jenis, ['MoU', 'MoA', 'IA'])) {
            return response()->json([], 400);
        }

        // Tentukan dokumen induk yang valid
        $allowed = match ($jenis) {
            'MoA' => ['MoU'],
            'IA' => ['MoU', 'MoA'],
            default => [],
        };

        // Ambil data dari DB
        $dokumen = RekapKerjaSama::whereIn('jenis_kerja_sama', $allowed)
            ->select('id', 'no_dokumen', 'judul_kerja_sama', 'mitra_kerja_sama')
            ->latest()
            ->get();

        // Tambahkan opsi "Tidak Ada Induk" di awal
        $dokumen->prepend((object)[
            'id' => 'none',
            'no_dokumen' => 'Tidak Ada Induk',
            'judul_kerja_sama' => '-',
            'mitra_kerja_sama' => '-',
        ]);

        return response()->json($dokumen);
    }


    public function create()
    {
        return view('inputrekapkerjasama');
    }

    public function edit($id)
    {
        $rekap = RekapKerjaSama::findOrFail($id);

        $dokumenInduk = collect();
        if ($rekap->jenis_kerja_sama === 'MoA') {
            $dokumenInduk = RekapKerjaSama::where('jenis_kerja_sama', 'MoU')
                ->where('id', '!=', $rekap->id)
                ->get();
        } elseif ($rekap->jenis_kerja_sama === 'IA') {
            $dokumenInduk = RekapKerjaSama::whereIn('jenis_kerja_sama', ['MoU', 'MoA'])
                ->where('id', '!=', $rekap->id)
                ->get();
        }

        $dokumenIndukId = $rekap->parent_id ?? null;

        return view('editrekapkerjasama', compact('rekap', 'dokumenInduk', 'dokumenIndukId'));
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

            $duration = (new \DateTime($request->tanggalMulai))->diff(new \DateTime($request->tanggalSelesai))->days + 1;

            $filePath = $rekap->dokumen_path;
            if ($request->hasFile('dokumenPendukung')) {
                if ($filePath && Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
                $filePath = $request->file('dokumenPendukung')->store('dokumen_kerja_sama', 'public');
            }

            $noDokInduk = optional(RekapKerjaSama::find($request->parent_id))->no_dokumen;

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
                'in_kind' => str_replace(',', '', $request->totalInKind) ?: 0,
                'total_in_kind' => str_replace(',', '', $request->totalInKind) ?: 0,
                'in_cash' => str_replace(',', '', $request->inCash) ?: 0,
                'total_in_cash' => str_replace(',', '', $request->totalInCash) ?: 0,
                'jumlah_implementasi' => $request->jumlahImplementasi ?? 0,
                'dokumen_path' => $filePath,
                'parent_id' => $request->parent_id,
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
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e instanceof \Illuminate\Validation\ValidationException ? $e->errors() : []
            ];

            return $request->wantsJson() || $request->ajax()
                ? response()->json($response, 500)
                : back()->withErrors($e->getMessage());
        }
    }

    public function lihatPDF($id)
    {
        $rekap = RekapKerjaSama::findOrFail($id);
        $disk = Storage::disk('public');

        if (!$disk->exists($rekap->dokumen_path)) abort(404);

        return response($disk->get($rekap->dokumen_path), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
