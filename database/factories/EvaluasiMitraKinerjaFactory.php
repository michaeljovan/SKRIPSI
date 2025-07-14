<?php

namespace Database\Factories;

use App\Models\EvaluasiMitraKinerja;
use App\Models\RekapKerjaSama;
use Illuminate\Database\Eloquent\Factories\Factory;

class EvaluasiMitraKinerjaFactory extends Factory
{
    protected $model = EvaluasiMitraKinerja::class;

    public function definition(): array
    {
        return [
            'rekap_id' => RekapKerjaSama::factory(),
            'nodok' => $this->faker->unique()->bothify('ND-###'),
            'mitra' => $this->faker->company,

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
            'lainlainlabel' => 'Kemampuan Adaptasi',
            'lainlainnilai' => rand(1, 5),
            'komentar' => $this->faker->sentence,
        ];
    }
}
