<?php

namespace Tests\Unit;

use Tests\TestCase;
use Carbon\Carbon;
use App\Models\EvaluasiLink;

class EvaluasiLinkTest extends TestCase
{
    /** @test */
    public function link_usable_jika_belum_dipakai_belum_dibatalkan_dan_belum_kadaluarsa()
    {
        Carbon::setTestNow(Carbon::parse('2025-09-05 10:00:00')); // waktu “sekarang” uji
        $link = new EvaluasiLink([
            'expires_at'      => Carbon::now()->addMinutes(30),
            'used_at'         => null,
            'invalidated_at'  => null,
        ]);

        $this->assertTrue($link->isUsable());
    }

    /** @test */
    public function link_tidak_usable_jika_sudah_kadaluarsa()
    {
        Carbon::setTestNow(Carbon::parse('2025-09-05 10:00:00'));
        $link = new EvaluasiLink([
            'expires_at'      => Carbon::now()->subMinute(), // sudah lewat
            'used_at'         => null,
            'invalidated_at'  => null,
        ]);

        $this->assertFalse($link->isUsable());
    }

    /** @test */
    public function link_tidak_usable_jika_sudah_dipakai()
    {
        Carbon::setTestNow(Carbon::parse('2025-09-05 10:00:00'));
        $link = new EvaluasiLink([
            'expires_at'      => Carbon::now()->addHour(),
            'used_at'         => Carbon::now(), // sudah dipakai
            'invalidated_at'  => null,
        ]);

        $this->assertFalse($link->isUsable());
    }

    /** @test */
    public function link_tidak_usable_jika_sudah_dibatalkan()
    {
        Carbon::setTestNow(Carbon::parse('2025-09-05 10:00:00'));
        $link = new EvaluasiLink([
            'expires_at'      => Carbon::now()->addHour(),
            'used_at'         => null,
            'invalidated_at'  => Carbon::now(), // dibatalkan
        ]);

        $this->assertFalse($link->isUsable());
    }
}
