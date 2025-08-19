<?php

namespace App\Mail;

use App\Models\RekapKerjaSama;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MitraKepuasanLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public $rekap;
    public $tautanForm;

    public function __construct(RekapKerjaSama $rekap, string $tautanForm)
    {
        $this->rekap = $rekap;
        $this->tautanForm = $tautanForm;
    }

    public function build()
    {
        return $this->subject('Form Evaluasi Kepuasan Mitra')
            ->view('emails.mitra_kepuasan_link_html'); // html/css murni
    }
}
