<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelaksanaanKerjaSama extends Model
{
    use HasFactory;
    protected $table = 'pelaksanaankerjasama';
    protected $primaryKey = 'id';

    protected $fillable = [
        'idrekap',
        'ruang_lingkup',
        'jumlah_dosen_terlibat',    // kolom baru
        'jumlah_mahasiswa_terlibat', // kolom baru
        'dosen_terlibat',
        'mahasiswa_terlibat',
        'anggaran_ukdw',
        'hasil_pelaksanaan',
        'tautan_link_kegiatan',
        'dokumen_kegiatan',
    ];


    // app/Models/PelaksanaanKerjaSama.php

    public function rekap()
    {
        return $this->belongsTo(RekapKerjaSama::class, 'idrekap');
    }
}
