<?php

// app/Mail/EvaluasiLinkMail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EvaluasiLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public $rekap;
    public $signedUrl;
    public $expiresAt;
    public $context;

    public function __construct($rekap, string $signedUrl, \Carbon\Carbon $expiresAt, string $context = 'kinerja')
    {
        $this->rekap = $rekap;
        $this->signedUrl = $signedUrl;
        $this->expiresAt = $expiresAt;
        $this->context = $context;
    }

    public function build()
    {
        $subject = $this->context === 'kepuasan'
            ? 'Tautan Evaluasi Kepuasan Mitra (Berlaku Terbatas)'
            : 'Tautan Evaluasi Kinerja Mitra (Berlaku Terbatas)';

        return $this->subject($subject)
            ->view('emails.evaluasi_link');
    }
}
