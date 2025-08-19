<?php

namespace App\Mail;

use App\Models\RekapKerjaSama;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MitraEvaluasiLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public RekapKerjaSama $rekap,
        public string $tautanForm
    ) {}

    public function build()
    {
        return $this->subject('Tautan Form Evaluasi Mitra Kinerja')
            ->view('emails.mitra_evaluasi_link_html'); // <- pakai view HTML
    }
}
