<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class MitraEvaluasiLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public $rekap;
    public $signedUrl;
    public $expiresAt;
    public $context;

    public function __construct($rekap, string $signedUrl, Carbon $expiresAt, string $context = 'kinerja')
    {
        $this->rekap = $rekap;
        $this->signedUrl = $signedUrl;
        $this->expiresAt = $expiresAt;
        $this->context = $context;
    }

    public function build()
    {
        return $this->subject('Link Evaluasi')
            ->view('emails.evaluasi_link'); // <- sesuaikan dengan file yang sudah ada
    }
}
