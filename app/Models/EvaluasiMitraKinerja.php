<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluasiMitraKinerja extends Model
{
    use HasFactory;
    protected $primaryKey = 'idkinerja';
    public $incrementing = true;
    protected $table = 'EvaluasiMitraKinerja';

    // Di dalam model EvaluasiMitraKinerja.php
    protected $appends = [
        'integritas_text',
        'keahlian_text',
        'komunikasi_text',
        'kerjasamatim_text',
        'pengembangandiri_text',
        'kreativitas_text',
        'bahasaasing_text',
        'teknologi_text',
        'manajerial_text',
        'analisis_text',
        'laporan_text',
        'inovasi_text',
        'lainlainnilai_text'
    ];

    // Accessor untuk mengubah nilai numerik ke teks
    public function getIntegritasTextAttribute()
    {
        return $this->convertToText($this->integritas);
    }

    public function getKeahlianTextAttribute()
    {
        return $this->convertToText($this->keahlian);
    }

    public function getKomunikasiTextAttribute()
    {
        return $this->convertToText($this->komunikasi);
    }
    public function getKerjasamatimTextAttribute()
    {
        return $this->convertToText($this->kerjasamatim);
    }
    public function getpengembangandiriTextAttribute()
    {
        return $this->convertToText($this->pengembangandiri);
    }
    public function getkreativitasTextAttribute()
    {
        return $this->convertToText($this->kreativitas);
    }
    public function getbahasaasingTextAttribute()
    {
        return $this->convertToText($this->bahasaasing);
    }
    public function getteknologiTextAttribute()
    {
        return $this->convertToText($this->teknologi);
    }
    public function getmanajerialTextAttribute()
    {
        return $this->convertToText($this->manajerial);
    }
    public function getanalisisTextAttribute()
    {
        return $this->convertToText($this->analisis);
    }
    public function getlaporanTextAttribute()
    {
        return $this->convertToText($this->laporan);
    }
    public function getinovasiTextAttribute()
    {
        return $this->convertToText($this->inovasi);
    }
    public function getlainlainlabelTextAttribute()
    {
        return $this->convertToText($this->lainlainlabel);
    }


    private function convertToText($value)
    {
        $map = [
            5 => 'Sangat Tinggi',
            4 => 'Tinggi',
            3 => 'Cukup',
            2 => 'Kurang',
            1 => 'Sangat Kurang'
        ];

        return $map[$value] ?? '-';
    }

    protected $fillable = [
        'rekap_id',
        'nodok',
        'mitra',
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
        'komentar'
    ];

    protected $casts = [
        'integritas' => 'integer',
        'keahlian' => 'integer',
        'komunikasi' => 'integer',
        'kerjasamatim' => 'integer',
        'pengembangandiri' => 'integer',
        'kreativitas' => 'integer',
        'bahasaasing' => 'integer',
        'teknologiinformasi' => 'integer',
        'manajerial' => 'integer',
        'analisis' => 'integer',
        'laporan' => 'integer',
        'inovasi' => 'integer',
        'lainlainnilai' => 'integer',
    ];

    public function rekapKerjasama()
    {
        return $this->belongsTo(RekapKerjaSama::class, 'rekap_id');
    }

    public function rekap()
    {
        return $this->belongsTo(RekapKerjasama::class, 'rekap_id');
    }
}
