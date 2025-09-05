<?php

namespace Tests\Unit;

use Tests\TestCase;
use Carbon\Carbon;

class EmailExpiryViewTest extends TestCase
{
    /** @test */
    public function view_email_menampilkan_tanggal_kadaluarsa_dengan_wib()
    {
        // set timezone & "sekarang" supaya deterministik
        config(['app.timezone' => 'Asia/Jakarta']);
        Carbon::setTestNow(Carbon::parse('2025-09-05 10:00:00', 'Asia/Jakarta'));

        // expires 45 menit lagi
        $expiresAt = Carbon::now()->addMinutes(45);

        // minimal data yang dibutuhkan oleh view:
        $fakeUrl = 'https://example.test/evaluasi?token=dummy';
        // kalau view kamu menggunakan $signedUrl ?? $url, aman isi keduanya salah satu
        $data = [
            'expiresAt' => $expiresAt,
            'signedUrl' => $fakeUrl,  // atau pakai 'url' => $fakeUrl
            // tambahkan variabel lain jika view kamu referensikan:
            // 'context' => 'kinerja',
            // 'mitraName' => 'PT Contoh',
        ];

        $html = view('emails.evaluasi_link', $data)->render();

        $expectedText = 'Berlaku s.d. ' . $expiresAt->timezone('Asia/Jakarta')->format('d/m/Y H:i') . ' WIB';

        $this->assertStringContainsString($expectedText, $html);
        $this->assertStringContainsString($fakeUrl, $html); // tombol mengarah ke URL yang dikirim
    }
}
