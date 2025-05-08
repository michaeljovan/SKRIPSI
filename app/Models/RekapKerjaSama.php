<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekapKerjaSama extends Model
{
    use HasFactory;
    protected $table = 'rekapkerjasama';
    protected $primaryKey = 'id';

    protected $fillable = [
        'no_dokumen',
        'unit',
        'mitra_kerja_sama',
        'judul_kerja_sama',
        'bentuk_kerja_sama',
        'bentuk_kerja_sama_text',
        'pihak_ukdw',
        'pihak_mitra',
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
        'is_mitra'
    ];

    protected $casts = [
        'bentuk_kerja_sama' => 'array',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function laporanPelaksanaan()
    {
        return $this->hasOne(PelaksanaanKerjaSama::class, 'idrekap', 'id');
    }

    public function rekap()
    {
        return $this->belongsTo(PelaksanaanKerjaSama::class, 'idrekap');
    }

    public function evaluasiMitraKinerja()
    {
        return $this->hasOne(EvaluasiMitraKinerja::class, 'rekap_id');
    }

    public function rekapKerjaSama()
    {
        return $this->belongsTo(RekapKerjaSama::class, 'rekap_id');
    }
    public function evaluasiMitra()
    {
        return $this->hasOne(EvaluasiMitra::class, 'rekap_id');
    }
}
