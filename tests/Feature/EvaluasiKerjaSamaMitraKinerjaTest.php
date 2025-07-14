<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RekapKerjaSama;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvaluasiKerjaSamaMitraKinerjaTest extends TestCase
{
    use RefreshDatabase;

    public function test_form_evaluasi_mitra_berhasil_disimpan()
    {
        $user = User::factory()->create(['role' => 'staff']);
        $this->actingAs($user);

        $rekap = RekapKerjaSama::factory()->create();

        $payload = [
            'rekap_id' => $rekap->id,
            'nodok' => 'ND-001',
            'mitra' => 'PT Contoh Mitra',
            'integritas' => 'Sangat Tinggi',
            'keahlian' => 'Tinggi',
            'komunikasi' => 'Tinggi',
            'kerjasamatim' => 'Sangat Tinggi',
            'pengembangandiri' => 'Tinggi',
            'kreativitas' => 'Sangat Tinggi',
            'bahasaasing' => 'Cukup',
            'teknologi' => 'Tinggi',
            'manajerial' => 'Tinggi',
            'analisis' => 'Sangat Tinggi',
            'laporan' => 'Tinggi',
            'inovasi' => 'Sangat Tinggi',
            'lainlainlabel' => 'Kemampuan Beradaptasi',
            'lainlainnilai' => 'Tinggi',
            'komentar' => 'Sangat baik dalam kolaborasi tim.',
        ];

        $response = $this->post(route('EvaluasiMitraKinerja.store'), $payload);
        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Evaluasi berhasil disimpan');

        $this->assertDatabaseHas('EvaluasiMitraKinerja', [
            'nodok' => 'ND-001',
            'mitra' => 'PT Contoh Mitra',
            'analisis' => 5,
            'komentar' => 'Sangat baik dalam kolaborasi tim.',
        ]);
    }
}
