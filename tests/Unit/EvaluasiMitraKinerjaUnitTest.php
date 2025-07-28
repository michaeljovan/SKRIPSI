<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use App\Http\Controllers\EvaluasiMitraKinerjaController;
use App\Models\EvaluasiMitraKinerja;
use App\Models\RekapKerjaSama;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EvaluasiMitraKinerjaUnitTest extends TestCase
{
    use RefreshDatabase;

    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new EvaluasiMitraKinerjaController();
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function memetakan_nilai_teks_ke_angka_dengan_benar()
    {
        $valueMap = [
            'Sangat Tinggi' => 5,
            'Tinggi' => 4,
            'Cukup' => 3,
            'Kurang' => 2,
            'Sangat Kurang' => 1
        ];

        foreach ($valueMap as $text => $number) {
            $this->assertEquals(
                $number,
                $this->invokePrivateMethod($this->controller, 'mapNilai', [$text])
            );
        }
    }

    private function getCompleteEvaluationData($withFile = false)
    {
        $data = [
            'rekap_id' => 1,
            'nodok' => 'DOC-001',
            'mitra' => 'Mitra Test',
            'integritas' => 'Tinggi',
            'keahlian' => 'Cukup',
            'komunikasi' => 'Sangat Tinggi',
            'kerjasamatim' => 'Tinggi',
            'pengembangandiri' => 'Cukup',
            'kreativitas' => 'Kurang',
            'bahasaasing' => 'Sangat Kurang',
            'teknologi' => 'Tinggi',
            'manajerial' => 'Cukup',
            'analisis' => 'Tinggi',
            'laporan' => 'Sangat Tinggi',
            'inovasi' => 'Tinggi',
            'komentar' => 'Komentar test'
        ];

        if ($withFile) {
            $data['pdfFile'] = UploadedFile::fake()->create('document.pdf', 1000);
        }

        return $data;
    }

    /** @test */
    public function dapat_menyimpan_data_evaluasi_dengan_nilai_yang_dipetakan()
    {
        // Buat dummy RekapKerjaSama agar tidak error foreign key
        RekapKerjaSama::factory()->create(['id' => 1]);

        $request = new Request($this->getCompleteEvaluationData());

        $response = $this->controller->store($request);

        $this->assertNotNull($response);

        $this->assertDatabaseHas('evaluasimitrakinerja', [
            'nodok' => 'DOC-001',
            'integritas' => 4, // 'Tinggi'
            'komunikasi' => 5  // 'Sangat Tinggi'
        ]);
    }

    /** @test */
    public function dapat_mengunggah_file_dengan_benar()
    {
        Storage::fake('public');

        RekapKerjaSama::factory()->create(['id' => 1]);

        $data = $this->getCompleteEvaluationData(true);

        $request = Request::create('/store', 'POST', $data, [], [
            'pdfFile' => $data['pdfFile']
        ]);

        $response = $this->controller->store($request);

        // Pastikan file benar-benar disimpan
        Storage::disk('public')->assertExists('evaluasi_pdf/' . $data['pdfFile']->hashName());

        $this->assertDatabaseHas('evaluasimitrakinerja', [
            'file_pdf' => 'evaluasi_pdf/' . $data['pdfFile']->hashName()
        ]);
    }

    /** @test */
   public function dapat_menghapus_data_evaluasi_dan_memperbarui_status_rekap()
    {
        $rekap = RekapKerjaSama::factory()->create(['id' => 1, 'is_kinerja' => true]);

        $evaluasi = EvaluasiMitraKinerja::factory()->create([
            'rekap_id' => $rekap->id
        ]);

        $response = $this->controller->delete($evaluasi->idkinerja);

        $this->assertEquals([
            'success' => true,
            'message' => 'Hasil evaluasi berhasil dihapus'
        ], $response->getData(true));

        $this->assertDatabaseMissing('evaluasimitrakinerja', ['idkinerja' => $evaluasi->idkinerja]);

        $rekap->refresh();
        $this->assertEquals(false, $rekap->is_kinerja);
    }

    /** @test */
    public function menangani_id_tidak_valid_saat_menghapus()
    {
        $response = $this->controller->delete('invalid-id');
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals([
            'success' => false,
            'message' => 'ID tidak valid'
        ], $response->getData(true));
    }

    /**
     * Helper method to invoke private methods
     */
    protected function invokePrivateMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
