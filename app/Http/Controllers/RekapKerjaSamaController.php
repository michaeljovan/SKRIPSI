<?php

namespace App\Http\Controllers;

use App\Models\RekapKerjaSama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Services\RekapKerjaSamaService;
use App\Models\EvaluasiKinerjaOtp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\EvaluasiKinerjaOtpMail;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class RekapKerjaSamaController extends Controller
{
    protected $service;

    public function __construct(RekapKerjaSamaService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $query = RekapKerjaSama::with('induk');

        // ========== Filter dasar ==========
        $filters = [
            'no_dokumen'       => 'like',
            'unit'             => '=',
            'mitra_kerja_sama' => 'like',
            'kategori'         => '=',
            'judul_kerja_sama' => 'like',
            'jenis_kerja_sama' => '=',
            'is_laporan'       => '=',
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
                $query->where('bentuk_kerja_sama', 'like', '%' . trim($bentuk) . '%');
            }
        }

        if ($request->filled('mitra')) {
            $query->where('mitra_kerja_sama', 'like', '%' . $request->mitra . '%');
        }

        if ($request->filled('judul')) {
            $query->where('judul_kerja_sama', 'like', '%' . $request->judul . '%');
        }

        // ========== Urutkan input terbaru ==========
        $query->orderByDesc('created_at')
            ->orderByDesc('id');

        return view('datadokumenkerjasama', [
            'rekapKerjaSama' => $query->paginate(15)->appends($request->query()),
        ]);
    }


    public function cekNoDokumen(Request $request)
    {
        return response()->json(['exists' => $this->service->noDokumenExists($request->no_dokumen)]);
    }

    public function store(Request $request)
    {
        try {
            if ($request->filled('parent_id') && $request->input('parent_id') === 'none') {
                $request->merge(['parent_id' => null]);
            }

            $validated = $request->validate([
                'noDokumen'         => 'required|unique:rekapkerjasama,no_dokumen',
                'unit'              => 'required',
                'mitraKerjaSama'    => 'required',
                'judulKerjaSama'    => 'required',
                'bentukKerjaSama'   => 'required|array|min:1',
                'bentukKerjaSama.*' => 'string|in:Penelitian,Pendidikan,Pengabdian',
                'jenisKerjaSama'    => 'required|string|in:MoU,MoA,IA',
                'jenisPermohonan'   => 'required|in:baru,perpanjang',
                'dokumenPerpanjang' => 'nullable|required_if:jenisPermohonan,perpanjang|integer|exists:rekapkerjasama,id',
                'pihakUKDW'         => 'required',
                'pihakMitra'        => 'required',
                'emailMitra'        => 'required|email',
                'tanggalMulai'      => 'required|date|before_or_equal:tanggalSelesai',
                'tanggalSelesai'    => 'required|date|after_or_equal:tanggalMulai',
                'kategori'          => 'required|string|in:nasional,internasional',
                'inKind'            => 'nullable|string',
                'inCash'            => 'nullable|string',
                'totalInKind'       => 'nullable|string',
                'totalInCash'       => 'nullable|string',
                'jumlahImplementasi' => 'nullable|integer|min:0',
                'dokumenPendukung'  => 'required|file|mimes:pdf|max:5120',
            ], [
                'bentukKerjaSama.min'           => 'Pilih minimal satu bentuk kerja sama.',
                'tanggalSelesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
                'dokumenPendukung.max'          => 'Ukuran dokumen maksimal 5MB.',
            ]);

            $parentId = null;
            if ($validated['jenisKerjaSama'] !== 'MoU' && $validated['jenisPermohonan'] === 'perpanjang') {
                $parentId = (int) $validated['dokumenPerpanjang'];
            }

            $duration = (new \DateTime($validated['tanggalMulai']))
                ->diff(new \DateTime($validated['tanggalSelesai']))->days + 1;

            $filePath = $request->file('dokumenPendukung')->store('dokumen_kerja_sama', 'public');

            $parseMoney = function ($v) {
                if ($v === null || $v === '') return null;
                $s = preg_replace('/\s+/', '', (string) $v);
                $s = preg_replace('/[^0-9.,]/', '', $s);

                $lastComma = strrpos($s, ',');
                $lastDot   = strrpos($s, '.');
                $decSep = null;
                if ($lastComma !== false || $lastDot !== false) {
                    if ($lastComma === false) $decSep = '.';
                    elseif ($lastDot === false) $decSep = ',';
                    else $decSep = ($lastComma > $lastDot) ? ',' : '.';
                }

                if ($decSep === ',') {
                    $s = str_replace('.', '', $s);
                    $s = str_replace(',', '.', $s);
                } else {
                    $s = str_replace(',', '', $s);
                }

                if ($s === '' || $s === '.') return null;
                return round((float) $s, 2);
            };

            $rekap = RekapKerjaSama::create([
                'no_dokumen'          => $validated['noDokumen'],
                'unit'                => $validated['unit'],
                'mitra_kerja_sama'    => $validated['mitraKerjaSama'],
                'judul_kerja_sama'    => $validated['judulKerjaSama'],
                'bentuk_kerja_sama'   => implode(', ', $validated['bentukKerjaSama']),
                'jenis_kerja_sama'    => $validated['jenisKerjaSama'],
                'pihak_ukdw'          => $validated['pihakUKDW'],
                'pihak_mitra'         => $validated['pihakMitra'],
                'email_pihak_mitra'   => $validated['emailMitra'],
                'tanggal_mulai'       => $validated['tanggalMulai'],
                'tanggal_selesai'     => $validated['tanggalSelesai'],
                'masa_berlaku'        => $duration,
                'kategori'            => $validated['kategori'],
                'in_kind'             => $parseMoney($request->input('inKind')),
                'in_cash'             => $parseMoney($request->input('inCash')),
                'total_in_kind'       => $parseMoney($request->input('totalInKind')),
                'total_in_cash'       => $parseMoney($request->input('totalInCash')),
                'jumlah_implementasi' => $request->input('jumlahImplementasi') !== null
                    ? (int) $request->input('jumlahImplementasi') : null,
                'dokumen_path'        => $filePath,
                'parent_id'           => $parentId,
            ]);

            return response()->json([
                'success'  => true,
                'message'  => 'Data kerja sama berhasil disimpan!',
                'redirect' => route('data_kerja_sama'),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
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

    public function options()
    {
        // ambil id current kalau dikirim (?exclude_id=123)
        $excludeId = request('exclude_id');
        $q = \App\Models\RekapKerjaSama::query()
            ->select('id', 'no_dokumen', 'judul_kerja_sama')
            ->orderBy('no_dokumen');

        if ($excludeId) $q->where('id', '!=', $excludeId);

        return response()->json($q->get());
    }


    public function create()
    {
        // objek kosong supaya blade aman diakses (create)
        $rekap = new RekapKerjaSama();

        // dropdown: semua dokumen (terbaru dulu)
        $semuaDokumen = RekapKerjaSama::select('id', 'no_dokumen', 'judul_kerja_sama')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        // default radio: Baru
        $defaultPermohonan = old('jenisPermohonan', 'baru');

        return view('inputrekapkerjasama', compact('rekap', 'semuaDokumen', 'defaultPermohonan'));
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
            $rekap = \App\Models\RekapKerjaSama::findOrFail($id);

            // Normalisasi: kosong => null (induk opsional)
            $request->merge([
                'parent_id' => $request->filled('parent_id') ? $request->input('parent_id') : null,
            ]);

            // RULES & MESSAGES (kunci uang pakai camelCase agar konsisten dengan store)
            $rules = [
                'noDokumen'          => ['required', \Illuminate\Validation\Rule::unique('rekapkerjasama', 'no_dokumen')->ignore($rekap->id, 'id')],
                'unit'               => ['required'],
                'mitraKerjaSama'     => ['required'],
                'judulKerjaSama'     => ['required'],
                'bentukKerjaSama'    => ['required', 'array'],
                'bentukKerjaSama.*'  => ['string', 'in:Penelitian,Pendidikan,Pengabdian'],
                'jenisKerjaSama'     => ['required', 'in:MoU,MoA,IA'],
                'pihakUKDW'          => ['required'],
                'pihakMitra'         => ['required'],
                'emailMitra'         => ['required', 'email'],
                'tanggalMulai'       => ['required', 'date'],
                'tanggalSelesai'     => ['required', 'date', 'after_or_equal:tanggalMulai'],
                'kategori'           => ['required', 'in:nasional,internasional'],
                'dokumenPendukung'   => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
                'parent_id'          => ['nullable', 'integer', 'exists:rekapkerjasama,id'],

                // uang (pakai format bebas, diparse manual)
                'inKind'             => ['nullable', 'string'],
                'totalInKind'        => ['nullable', 'string'],
                'inCash'             => ['nullable', 'string'],
                'totalInCash'        => ['nullable', 'string'],

                'jumlahImplementasi' => ['nullable', 'numeric'],
            ];

            $messages = [
                'noDokumen.required' => 'No Dokumen wajib diisi.',
                'noDokumen.unique'   => 'No Dokumen sudah digunakan.',
                'bentukKerjaSama.required' => 'Pilih minimal satu Bentuk Kerja Sama.',
                'emailMitra.email'   => 'Format email penanggung jawab mitra tidak valid.',
                'tanggalSelesai.after_or_equal' => 'Tanggal Selesai harus sama atau setelah Tanggal Mulai.',
                'dokumenPendukung.mimes' => 'Dokumen pendukung harus PDF.',
                'dokumenPendukung.max'   => 'Ukuran dokumen pendukung maksimal 5MB.',
                'parent_id.exists'       => 'Dokumen induk tidak ditemukan.',
            ];

            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $messages);
            $validator->after(function ($v) use ($request, $rekap) {
                // MoU tidak boleh punya induk
                if ($request->input('jenisKerjaSama') === 'MoU' && $request->filled('parent_id')) {
                    $v->errors()->add('parent_id', 'MoU tidak memerlukan dokumen induk.');
                }
                // parent_id tidak boleh menunjuk ke dirinya sendiri
                if ($request->filled('parent_id') && (int)$request->input('parent_id') === (int)$rekap->id) {
                    $v->errors()->add('parent_id', 'Dokumen induk tidak boleh dokumen ini sendiri.');
                }
                // Validasi tipe induk
                if ($request->filled('parent_id')) {
                    $parent = \App\Models\RekapKerjaSama::find($request->input('parent_id'));
                    if ($parent) {
                        $jenis = $request->input('jenisKerjaSama');
                        if ($jenis === 'MoA' && $parent->jenis_kerja_sama !== 'MoU') {
                            $v->errors()->add('parent_id', 'Induk MoA harus bertipe MoU.');
                        }
                        if ($jenis === 'IA' && !in_array($parent->jenis_kerja_sama, ['MoU', 'MoA'])) {
                            $v->errors()->add('parent_id', 'Induk IA harus bertipe MoU atau MoA.');
                        }
                    }
                }
            });

            if ($validator->fails()) {
                throw new \Illuminate\Validation\ValidationException($validator);
            }

            $validated = $validator->validated();

            // MoU: paksa induk null
            if ($validated['jenisKerjaSama'] === 'MoU') {
                $validated['parent_id'] = null;
            }

            // Tanggal & masa berlaku
            $mulai   = \Carbon\Carbon::parse($validated['tanggalMulai'])->startOfDay();
            $selesai = \Carbon\Carbon::parse($validated['tanggalSelesai'])->endOfDay();
            $duration = $mulai->diffInDays($selesai) + 1;

            // Upload file (replace)
            $filePath = $rekap->dokumen_path;
            if ($request->hasFile('dokumenPendukung')) {
                if ($filePath && \Storage::disk('public')->exists($filePath)) {
                    \Storage::disk('public')->delete($filePath);
                }
                $filePath = $request->file('dokumenPendukung')->store('dokumen_kerja_sama', 'public');
            }

            // Normalisasi rupiah/decimal
            $parseMoney = function ($v) {
                if ($v === null || $v === '') return null;
                $s = preg_replace('/\s+/', '', (string) $v);
                $s = preg_replace('/[^0-9.,]/', '', $s);
                $lastComma = strrpos($s, ',');
                $lastDot   = strrpos($s, '.');
                $decSep = null;
                if ($lastComma !== false || $lastDot !== false) {
                    if ($lastComma === false) $decSep = '.';
                    elseif ($lastDot === false) $decSep = ',';
                    else $decSep = ($lastComma > $lastDot) ? ',' : '.';
                }
                if ($decSep === ',') {
                    $s = str_replace('.', '', $s);
                    $s = str_replace(',', '.', $s);
                } else {
                    $s = str_replace(',', '', $s);
                }
                if ($s === '' || $s === '.') return null;
                return round((float) $s, 2);
            };

            // Tentukan status baru (kecuali sudah dihentikan)
            $now = \Carbon\Carbon::now('Asia/Jakarta');
            $statusBaru = $rekap->status === 'dihentikan'
                ? 'dihentikan'
                : ($selesai->lt($now) ? 'selesai' : 'aktif');

            // Update
            $rekap->update([
                'no_dokumen'          => $validated['noDokumen'],
                'unit'                => $validated['unit'],
                'mitra_kerja_sama'    => $validated['mitraKerjaSama'],
                'judul_kerja_sama'    => $validated['judulKerjaSama'],
                'bentuk_kerja_sama'   => implode(', ', $validated['bentukKerjaSama']),
                'jenis_kerja_sama'    => $validated['jenisKerjaSama'],
                'pihak_ukdw'          => $validated['pihakUKDW'],
                'pihak_mitra'         => $validated['pihakMitra'],
                'email_pihak_mitra'   => $validated['emailMitra'],
                'tanggal_mulai'       => $mulai->toDateString(),
                'tanggal_selesai'     => $selesai->toDateString(),
                'masa_berlaku'        => $duration,
                'kategori'            => $validated['kategori'],

                // uang (decimal 2)
                'in_kind'             => $parseMoney($request->input('inKind')),
                'total_in_kind'       => $parseMoney($request->input('totalInKind')),
                'in_cash'             => $parseMoney($request->input('inCash')),
                'total_in_cash'       => $parseMoney($request->input('totalInCash')),

                'jumlah_implementasi' => $request->filled('jumlahImplementasi')
                    ? (int) $request->input('jumlahImplementasi') : null,
                'dokumen_path'        => $filePath,
                'parent_id'           => $validated['parent_id'],

                // status baru (jangan override jika sudah dihentikan)
                'status'              => $statusBaru,
            ]);

            // Respons
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success'  => true,
                    'message'  => 'Data kerja sama berhasil diperbarui!',
                    'redirect' => route('data_kerja_sama'),
                ]);
            }

            return redirect()
                ->route('data_kerja_sama')
                ->with('success', 'Data kerja sama berhasil diperbarui!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'errors'  => $e->errors(),
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }
            return back()->with('error', $e->getMessage())->withInput();
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

    public function sendEvaluasiOtp(RekapKerjaSama $rekap, Request $request)
    {
        $staff = Auth::user();
        if (!$staff || !$staff->email) {
            return back()->with('error', 'Email staff tidak ditemukan.');
        }

        // generate OTP 6 digit
        $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // simpan hash & meta
        $record = EvaluasiKinerjaOtp::create([
            'rekap_id'     => $rekap->id,
            'staff_id'     => $staff->id ?? null,
            'code_hash'    => Hash::make($otp),
            'expires_at'   => now()->addMinutes(30),
            'sent_to_email' => $staff->email,
            'request_ip'   => $request->ip(),
            'user_agent'   => substr((string)$request->userAgent(), 0, 190),
        ]);

        // link OTP gate yang akan diberikan ke mitra
        $otpGateUrl = route('evaluasi.kinerja.otp.show', ['rekap' => $rekap->id]);

        // kirim email ke staff (staff akan teruskan OTP & link ke mitra)
        Mail::to($staff->email)->send(new EvaluasiKinerjaOtpMail($rekap, $otp, $otpGateUrl));

        return back()->with('success', 'OTP dan tautan evaluasi telah dikirim ke email Anda.');
    }


    public function stopForm($id)
    {
        $rekap = RekapKerjaSama::with('induk')->findOrFail($id);

        $today      = Carbon::today();
        $isSelesai  = ($rekap->status === 'selesai')
            || Carbon::parse($rekap->tanggal_selesai)->endOfDay()->lt(now());
        $mulai      = Carbon::parse($rekap->tanggal_mulai)->startOfDay();
        $newDurasi  = $mulai->diffInDays($today) + 1; // preview masa berlaku jika dihentikan hari ini

        // arahkan ke view yang kamu pakai sekarang
        return view('stopkerjasama', compact('rekap', 'today', 'isSelesai', 'newDurasi'));
    }

    public function stop(Request $request, $id)
    {
        $request->validate([
            'alasan' => ['required', 'string', 'min:5'],
        ], [
            'alasan.required' => 'Alasan wajib diisi.',
            'alasan.min'      => 'Alasan minimal 5 karakter.',
        ]);

        $today = Carbon::today();

        try {
            DB::transaction(function () use ($id, $request, $today) {
                $rekap = RekapKerjaSama::whereKey($id)->lockForUpdate()->firstOrFail();

                // Sudah selesai normal?
                if (
                    $rekap->status === 'selesai'
                    || Carbon::parse($rekap->tanggal_selesai)->endOfDay()->lt(now())
                ) {
                    throw new \RuntimeException('Kerja sama ini sudah berstatus selesai.');
                }

                // Sudah dihentikan?
                if ($rekap->status === 'dihentikan') {
                    throw new \RuntimeException('Kerja sama ini sudah dihentikan sebelumnya.');
                }

                // Jika fillable kamu belum mencakup field stop, lakukan set manual & save()
                $rekap->tanggal_selesai = $today->toDateString();
                $rekap->masa_berlaku    = Carbon::parse($rekap->tanggal_mulai)->startOfDay()->diffInDays($today) + 1;
                $rekap->status          = 'dihentikan';
                $rekap->stopped_at      = now();
                $rekap->stopped_reason  = $request->alasan;
                $rekap->save();
            });
        } catch (\RuntimeException $e) {
            return redirect()->route('data_kerja_sama')->with('info', $e->getMessage());
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menghentikan: ' . $e->getMessage());
        }

        return redirect()->route('data_kerja_sama')
            ->with('success', 'Kerja sama dihentikan per ' . $today->format('d/m/Y') . '.');
    }
}
