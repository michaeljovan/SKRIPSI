<?php

namespace Database\Factories;

use App\Models\EvaluasiMitra;
use App\Models\RekapKerjaSama;
use Illuminate\Database\Eloquent\Factories\Factory;

class EvaluasiMitraFactory extends Factory
{
    protected $model = EvaluasiMitra::class;

    public function definition(): array
    {
        return [
            'rekap_id' => RekapKerjaSama::factory(), // relasi
            'nodok' => $this->faker->uuid,
            'mitra' => $this->faker->company,

            // Skor 1–5
            'integritas' => rand(1, 5),
            'keahlian' => rand(1, 5),
            'komunikasi' => rand(1, 5),
            'kerjasamatim' => rand(1, 5),
            'pengembangandiri' => rand(1, 5),
            'kreativitas' => rand(1, 5),
            'bahasaasing' => rand(1, 5),
            'teknologi' => rand(1, 5),
            'manajerial' => rand(1, 5),
            'analisis' => rand(1, 5),
            'laporan' => rand(1, 5),
            'inovasi' => rand(1, 5),

            'lainlainlabel' => $this->faker->optional()->word(),
            'lainlainnilai' => $this->faker->optional()->numberBetween(1, 5),
            'komentar' => $this->faker->optional()->sentence(),
            'file_pdf' => 'evaluasi_pdf/dummy.pdf',
        ];
    }
}
