<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EvaluasiMitraPerorangan extends Model
{
    use HasFactory;

    protected $table = 'evaluasi_mitra_perorangan';

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
        'komentar',
        'lampiran_pdf_path',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function rekapKerjasama()
    {
        return $this->belongsTo(RekapKerjaSama::class, 'rekap_id');
    }
}
