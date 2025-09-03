<?php

// app/Models/EvaluasiLink.php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluasiLink extends Model
{
    protected $fillable = [
        'rekap_id','context','token_hash','expires_at','used_at','invalidated_at',
        'sent_to_email','created_by_staff_id','request_ip','user_agent'
    ];

    protected $casts = [
        'expires_at'     => 'datetime',
        'used_at'        => 'datetime',
        'invalidated_at' => 'datetime',
    ];

    // default table "evaluasi_links" sudah benar untuk nama model EvaluasiLink
    // kalau nama tabelmu beda, tambahkan: protected $table = 'nama_tabel';

    public function rekap()
    {
        return $this->belongsTo(\App\Models\RekapKerjaSama::class, 'rekap_id');
    }

    public function isUsable(): bool
    {
        return is_null($this->used_at)
            && is_null($this->invalidated_at)
            && $this->expires_at->isFuture();
    }
}
