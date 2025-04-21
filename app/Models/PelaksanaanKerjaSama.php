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
        'dosen_terlibat',
        'mahasiswa_terlibat',
        'anggaran_ukdw',
        'hasil_pelaksanaan',
        'tautan_link_kegiatan',
    ];
}
