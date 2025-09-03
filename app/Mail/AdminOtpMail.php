<?php

// app/Mail/AdminOtpMail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class AdminOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $rekap;
    public $otp;         // optional (kalau masih mau dipakai)
    public $signedUrl;   // <— penting
    public $expiresAt;   // <— penting
    public $context;

    public function __construct($rekap, ?string $otp = null, ?string $signedUrl = null, ?Carbon $expiresAt = null, string $context = 'kinerja')
    {
        $this->rekap     = $rekap;
        $this->otp       = $otp;
        $this->signedUrl = $signedUrl;
        $this->expiresAt = $expiresAt;
        $this->context   = $context;
    }

    public function build()
    {
        $subject = $this->context === 'kepuasan'
            ? 'Evaluasi Kepuasan Mitra (Tautan Berlaku Terbatas)'
            : 'Evaluasi Kinerja Mitra (Tautan Berlaku Terbatas)';

        return $this->subject($subject)
            ->view('emails.admin_otp_html'); // view yang kamu pakai
        // Catatan: public properties otomatis tersedia di Blade
    }
}
