<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EvaluasiMitraKinerja extends Model
{
    use HasFactory;

    protected $table = 'evaluasimitrakinerja'; // samakan dengan migration
    protected $primaryKey = 'idkinerja';
    public $incrementing = true;

    protected $fillable = [
        'rekap_id',
        'nodok',
        'mitra',

        // nilai (1-5)
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
        'file_pdf',

        // tambahan
        'pengisi_mitra',
    ];

    protected $casts = [
        'integritas'       => 'integer',
        'keahlian'         => 'integer',
        'komunikasi'       => 'integer',
        'kerjasamatim'     => 'integer',
        'pengembangandiri' => 'integer',
        'kreativitas'      => 'integer',
        'bahasaasing'      => 'integer',
        'teknologi'        => 'integer',
        'manajerial'       => 'integer',
        'analisis'         => 'integer',
        'laporan'          => 'integer',
        'inovasi'          => 'integer',
        'lainlainnilai'    => 'integer',
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
        'pdf_url',
    ];

    // === Accessor teks ===
    public function getIntegritasTextAttribute()      { return $this->toText($this->integritas); }
    public function getKeahlianTextAttribute()        { return $this->toText($this->keahlian); }
    public function getKomunikasiTextAttribute()      { return $this->toText($this->komunikasi); }
    public function getKerjasamatimTextAttribute()    { return $this->toText($this->kerjasamatim); }
    public function getPengembangandiriTextAttribute(){ return $this->toText($this->pengembangandiri); }
    public function getKreativitasTextAttribute()     { return $this->toText($this->kreativitas); }
    public function getBahasaasingTextAttribute()     { return $this->toText($this->bahasaasing); }
    public function getTeknologiTextAttribute()       { return $this->toText($this->teknologi); }
    public function getManajerialTextAttribute()      { return $this->toText($this->manajerial); }
    public function getAnalisisTextAttribute()        { return $this->toText($this->analisis); }
    public function getLaporanTextAttribute()         { return $this->toText($this->laporan); }
    public function getInovasiTextAttribute()         { return $this->toText($this->inovasi); }
    public function getLainlainnilaiTextAttribute()   { return $this->toText($this->lainlainnilai); }

    // PDF URL
    public function getPdfUrlAttribute()
    {
        return $this->file_pdf ? Storage::url($this->file_pdf) : null;
    }

    private function toText($value)
    {
        $map = [5=>'Sangat Tinggi',4=>'Tinggi',3=>'Cukup',2=>'Kurang',1=>'Sangat Kurang'];
        return $map[$value] ?? '-';
    }

    // Relasi
    public function rekapKerjasama()
    {
        return $this->belongsTo(RekapKerjaSama::class, 'rekap_id');
    }

    // alias
    public function rekap()
    {
        return $this->belongsTo(RekapKerjaSama::class, 'rekap_id');
    }
}
