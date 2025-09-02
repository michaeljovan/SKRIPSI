<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RekapKerjaSama extends Model
{
    use HasFactory;

    protected $table = 'rekapkerjasama';
    protected $primaryKey = 'id';
    public $timestamps = true;

    // Jika ingin status_display ikut muncul saat toArray()/JSON, uncomment baris di bawah:
    // protected $appends = ['status_display'];

    protected $fillable = [
        'parent_id',
        'jenis_permohonan', // opsional (baru/perpanjang)
        'no_dokumen',
        'unit',
        'mitra_kerja_sama',
        'judul_kerja_sama',
        'bentuk_kerja_sama',
        'jenis_kerja_sama',
        'pihak_ukdw',
        'pihak_mitra',
        'email_pihak_mitra',
        'tanggal_mulai',
        'tanggal_selesai',
        'masa_berlaku',
        'kategori',
        'in_kind',
        'total_in_kind',
        'in_cash',
        'total_in_cash',
        'jumlah_implementasi',
        'dokumen_path',
        'is_laporan',
        'is_kinerja',
        'is_mitra',

        // Kolom tambahan untuk fitur STOP
        'status',          // 'aktif' | 'selesai' | 'dihentikan'
        'stopped_at',
        'stopped_reason',

        // (opsional, jika kolom ini ada di DB kamu dan dipakai di controller update)
        'no_dokumen_induk',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'total_in_cash'   => 'decimal:2',
        'total_in_kind'   => 'decimal:2',
        'is_laporan'      => 'boolean',
        'is_kinerja'      => 'boolean',
        'is_mitra'        => 'boolean',
        'stopped_at'      => 'datetime',
    ];

    /* =========================
     * Relasi ke entitas lain
     * ========================= */

    public function laporanPelaksanaan()
    {
        return $this->hasOne(PelaksanaanKerjaSama::class, 'idrekap');
    }

    public function evaluasiMitraKinerja()
    {
        return $this->hasOne(EvaluasiMitraKinerja::class, 'rekap_id');
    }

    public function evaluasiMitra()
    {
        return $this->hasOne(EvaluasiMitra::class, 'rekap_id');
    }

    /* =========================
     * Relasi self-reference (induk/turunan)
     * ========================= */

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    // Alias agar kode lama yang pakai 'induk' tetap jalan
    public function induk()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    // Helper: apakah dokumen ini hasil perpanjangan
    public function getIsPerpanjangAttribute(): bool
    {
        return !is_null($this->parent_id);
    }

    // (Opsional) no dokumen induk
    public function getNoDokumenIndukAttribute(): ?string
    {
        return $this->parent?->no_dokumen;
    }

    /* =========================
     * Scopes (query helpers)
     * ========================= */

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopePerpanjang($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    public function scopeDihentikan($query)
    {
        return $query->where('status', 'dihentikan');
    }

    /**
     * Dokumen yang "berjalan" per hari ini:
     * status 'aktif' dan tanggal_selesai >= hari ini
     */
    public function scopeBerjalan($query)
    {
        return $query->where('status', 'aktif')
                     ->whereDate('tanggal_selesai', '>=', Carbon::today());
    }

    /* =========================
     * Helper status & durasi
     * ========================= */

    public function getIsAktifAttribute(): bool
    {
        return $this->status === 'aktif';
    }

    public function getIsDihentikanAttribute(): bool
    {
        return $this->status === 'dihentikan';
    }

    // Jangan anggap 'dihentikan' sebagai selesai walau tanggal lewat
    public function getIsSelesaiAttribute(): bool
    {
        if ($this->status === 'selesai') return true;
        if ($this->status === 'dihentikan') return false;

        return ($this->tanggal_selesai?->endOfDay()->lt(now())) ?? false;
    }

    /**
     * Accessor tampilan status untuk UI:
     * - 'dihentikan' jika dihentikan
     * - 'selesai' jika status 'selesai' ATAU aktif tapi tanggal sudah lewat
     * - 'aktif' selain itu
     */
    public function getStatusDisplayAttribute(): string
    {
        if ($this->status === 'dihentikan') return 'dihentikan';
        if ($this->status === 'selesai') return 'selesai';

        if ($this->tanggal_selesai && $this->tanggal_selesai->endOfDay()->lt(now())) {
            return 'selesai';
        }
        return 'aktif';
    }

    /**
     * Durasi (hari) jika dihentikan hari ini (inklusif).
     */
    public function durasiJikaStopHariIni(): int
    {
        $today = Carbon::today();
        if (!$this->tanggal_mulai) return 0;

        return $this->tanggal_mulai->diffInDays($today) + 1;
    }

    /**
     * Tandai sebagai dihentikan hari ini.
     */
    public function stopToday(string $reason): void
    {
        $today = Carbon::today();

        $this->tanggal_selesai = $today;
        $this->status          = 'dihentikan';
        $this->stopped_at      = now();
        $this->stopped_reason  = $reason;
        $this->save();
    }
}
