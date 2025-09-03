<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluasiMitraKinerjaPerorangan extends Model
{
    protected $table = 'evaluasi_mitra_kinerja_perorangan';

    protected $fillable = [
        'rekap_id',
        'tipe_responden',
        'nama_responden',
        'pengisi_mitra',
        'integritas',
        'keahlian',
        'komunikasi',
        'kerjasamatim',
        'pengembangandiri',
        'kreativitas',
        'bahasaasing',
        'teknologi',
        'manajerial',
        'analisis',
        'laporan',
        'inovasi',
        'lainlainlabel',
        'lainlainnilai',
        'komentar',
        'lampiran_pdf_path',
        'submitted_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public $timestamps = true; // karena migration di atas pakai timestamps

    public function rekap()
    {
        return $this->belongsTo(\App\Models\RekapKerjaSama::class, 'rekap_id', 'id');
    }
}
