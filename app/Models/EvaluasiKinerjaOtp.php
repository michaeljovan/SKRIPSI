<?php

// app/Models/EvaluasiKinerjaOtp.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluasiKinerjaOtp extends Model
{
    protected $table = 'evaluasi_kinerja_otps';
    protected $fillable = [
        'rekap_id','staff_id','code_hash','expires_at','used_at',
        'sent_to_email','request_ip','user_agent'
    ];
    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];
}
