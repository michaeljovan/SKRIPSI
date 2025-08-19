<?php

namespace App\Mail;

use App\Models\RekapKerjaSama;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $rekap;
    public $otp;
    public $otpGateUrl;
    public $context;

    public function __construct(RekapKerjaSama $rekap, string $otp, ?string $otpGateUrl = null, string $context = 'kinerja')
    {
        $this->rekap      = $rekap;
        $this->otp        = $otp;
        $this->otpGateUrl = $otpGateUrl;
        $this->context    = $context;
    }

    public function build()
    {
        $subject = $this->context === 'kepuasan'
            ? 'OTP Evaluasi Kepuasan Mitra (Admin)'
            : 'OTP Evaluasi Mitra Kinerja (Admin)';

        return $this->subject($subject)
                    ->view('emails.admin_otp_html'); // pakai view HTML yang sudah kamu punya
    }
}
