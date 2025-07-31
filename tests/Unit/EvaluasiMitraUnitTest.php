<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use Mockery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\EvaluasiMitraController;
use App\Models\EvaluasiMitra;
use App\Models\RekapKerjaSama;

class EvaluasiMitraUnitTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function dapat_menyimpan_data_evaluasi_mitra()
    {
        Storage::fake('public');

        $rekap = RekapKerjaSama::factory()->create();

        $controller = new EvaluasiMitraController();

        $request = Request::create('/evaluasi-mitra', 'POST', [
            'rekap_id' => $rekap->id,
            'nodok' => '123',
            'mitra' => 'Mitra A',
            'integritas' => 'Tinggi',
            'keahlian' => 'Tinggi',
            'komunikasi' => 'Tinggi',
            'kerjasamatim' => 'Tinggi',
            'pengembangandiri' => 'Tinggi',
            'kreativitas' => 'Tinggi',
            'bahasaasing' => 'Tinggi',
        ]);

        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
        $request->files->set('pdfFile', $file);

        $response = $controller->store($request);

        $this->assertDatabaseHas('evaluasimitra', [
            'nodok' => '123',
            'mitra' => 'Mitra A',
            'integritas' => 4, // 'Tinggi'
        ]);

        $this->assertEquals(302, $response->status());
    }

    /** @test */
    public function dapat_memperbarui_data_evaluasi_mitra()
    {
        Storage::fake('public');

        $eval = EvaluasiMitra::factory()->create([
            'integritas' => 3,
        ]);

        $controller = new EvaluasiMitraController();

        $request = Request::create("/evaluasi-mitra/{$eval->idmitra}", 'POST', [
            'integritas' => 'Sangat Tinggi',
            'keahlian' => 'Tinggi',
            'komunikasi' => 'Tinggi',
            'kerjasamatim' => 'Tinggi',
            'pengembangandiri' => 'Tinggi',
            'kreativitas' => 'Tinggi',
            'bahasaasing' => 'Tinggi',
        ]);

        $response = $controller->update($request, $eval->idmitra);

        $this->assertDatabaseHas('evaluasimitra', [
            'idmitra' => $eval->idmitra,
            'integritas' => 5,
        ]);

        $this->assertEquals(302, $response->status());
    }

    /** @test */
    public function dapat_menghapus_data_evaluasi_mitra()
    {
        $rekap = RekapKerjaSama::factory()->create(['is_mitra' => true]);

        $eval = EvaluasiMitra::factory()->create([
            'rekap_id' => $rekap->id,
        ]);

        $controller = new EvaluasiMitraController();
        $response = $controller->delete($eval->idmitra);

        $this->assertDatabaseMissing('evaluasimitra', ['idmitra' => $eval->idmitra]);

        $rekap->refresh();
        $this->assertEquals(false, $rekap->is_mitra);

        $this->assertEquals(200, $response->status());
    }
}
