<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EvaluasiMitraKinerja extends Model
{
    use HasFactory;

    protected $primaryKey = 'idkinerja';
    public $incrementing = true;
    protected $table = 'EvaluasiMitraKinerja';

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
        'komentar',
        'file_pdf'
    ];

    protected $casts = [
        'integritas' => 'integer',
        'keahlian' => 'integer',
        'komunikasi' => 'integer',
        'kerjasamatim' => 'integer',
        'pengembangandiri' => 'integer',
        'kreativitas' => 'integer',
        'bahasaasing' => 'integer',
        'teknologi' => 'integer', // perbaikan dari 'teknologiinformasi'
        'manajerial' => 'integer',
        'analisis' => 'integer',
        'laporan' => 'integer',
        'inovasi' => 'integer',
        'lainlainnilai' => 'integer',
    ];

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
        'lainlainnilai_text',
        'pdf_url'
    ];

    // === Accessor untuk tiap nilai ke bentuk teks ===
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

    public function getPengembangandiriTextAttribute()
    {
        return $this->convertToText($this->pengembangandiri);
    }

    public function getKreativitasTextAttribute()
    {
        return $this->convertToText($this->kreativitas);
    }

    public function getBahasaasingTextAttribute()
    {
        return $this->convertToText($this->bahasaasing);
    }

    public function getTeknologiTextAttribute()
    {
        return $this->convertToText($this->teknologi);
    }

    public function getManajerialTextAttribute()
    {
        return $this->convertToText($this->manajerial);
    }

    public function getAnalisisTextAttribute()
    {
        return $this->convertToText($this->analisis);
    }

    public function getLaporanTextAttribute()
    {
        return $this->convertToText($this->laporan);
    }

    public function getInovasiTextAttribute()
    {
        return $this->convertToText($this->inovasi);
    }

    // === FIXED: Accessor untuk lainlainnilai ===
    public function getLainlainnilaiTextAttribute()
    {
        return $this->convertToText($this->lainlainnilai);
    }

    // === PDF file URL accessor ===
    public function getPdfUrlAttribute()
    {
        return $this->file_pdf ? Storage::url($this->file_pdf) : null;
    }

    // === Shared method untuk mapping angka ke teks ===
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

    // === Relasi ===
    public function rekapKerjasama()
    {
        return $this->belongsTo(RekapKerjaSama::class, 'rekap_id');
    }

    public function rekap()
    {
        return $this->belongsTo(RekapKerjaSama::class, 'rekap_id');
    }
}
