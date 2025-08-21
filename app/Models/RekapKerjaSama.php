<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekapKerjaSama extends Model
{
    use HasFactory;

    protected $table = 'rekapkerjasama';
    protected $primaryKey = 'id';
    public $timestamps = true; 
    protected $fillable = [
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
        'parent_id' // <--- penting!
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'in_cash' => 'float',
        'in_kind' => 'float',
        'total_in_cash' => 'float',
        'total_in_kind' => 'float',
    ];

    // Relasi pelaksanaan
    public function laporanPelaksanaan()
    {
        return $this->hasOne(PelaksanaanKerjaSama::class, 'idrekap');
    }

    // Relasi evaluasi
    public function evaluasiMitraKinerja()
    {
        return $this->hasOne(EvaluasiMitraKinerja::class, 'rekap_id');
    }

    public function evaluasiMitra()
    {
        return $this->hasOne(EvaluasiMitra::class, 'rekap_id');
    }

    // 🔁 Dokumen induk
    public function parent()
    {
        return $this->belongsTo(RekapKerjaSama::class, 'parent_id');
    }

    // 🔁 Dokumen turunan
    public function children()
    {
        return $this->hasMany(RekapKerjaSama::class, 'parent_id');
    }

    public function induk()
    {
        return $this->belongsTo(RekapKerjaSama::class, 'parent_id');
    }
}
