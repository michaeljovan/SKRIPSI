<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EvaluasiMitra extends Model
{
    use HasFactory;

    // Pastikan nama tabel sesuai dengan migration (huruf kecil)
    protected $table = 'evaluasimitra';

    protected $primaryKey = 'idmitra';
    public $incrementing = true;

    protected $fillable = [
        'rekap_id',
        'nodok',
        'mitra',
        'pengisi_mitra',     // <--- TAMBAHAN

        // skor (1-5) disimpan string/angka -> kita cast ke integer di bawah
        'integritas',
        'keahlian',
        'komunikasi',
        'kerjasamatim',
        'pengembangandiri',
        'kreativitas',
        'bahasaasing',

        'komentar',
        'file_pdf',
    ];

    protected $casts = [
        'integritas'       => 'integer',
        'keahlian'         => 'integer',
        'komunikasi'       => 'integer',
        'kerjasamatim'     => 'integer',
        'pengembangandiri' => 'integer',
        'kreativitas'      => 'integer',
        'bahasaasing'      => 'integer',
    ];

    protected $appends = [
        'integritas_text',
        'keahlian_text',
        'komunikasi_text',
        'kerjasamatim_text',
        'pengembangandiri_text',
        'kreativitas_text',
        'bahasaasing_text',
        'pdf_url',
    ];

    // ===== Accessors teks untuk setiap skor =====
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

    // File URL
    public function getPdfUrlAttribute()
    {
        return $this->file_pdf ? Storage::url($this->file_pdf) : null;
    }

    // Mapping angka -> teks
    private function convertToText($value)
    {
        $map = [
            5 => 'Sangat Tinggi',
            4 => 'Tinggi',
            3 => 'Cukup',
            2 => 'Kurang',
            1 => 'Sangat Kurang',
        ];
        return $map[$value] ?? '-';
    }

    // Relasi
    public function rekapKerjasama()
    {
        return $this->belongsTo(RekapKerjaSama::class, 'rekap_id');
    }
}
