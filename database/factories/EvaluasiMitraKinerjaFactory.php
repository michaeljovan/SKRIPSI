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
            'nodok' => $this->faker->unique()->numerify('DOC-###'),
            'mitra' => $this->faker->company,
            'integritas' => $this->faker->numberBetween(1, 5),
            'keahlian' => $this->faker->numberBetween(1, 5),
            'komunikasi' => $this->faker->numberBetween(1, 5),
            'kerjasamatim' => $this->faker->numberBetween(1, 5),
            'pengembangandiri' => $this->faker->numberBetween(1, 5),
            'kreativitas' => $this->faker->numberBetween(1, 5),
            'bahasaasing' => $this->faker->numberBetween(1, 5),
            'teknologi' => $this->faker->numberBetween(1, 5),
            'manajerial' => $this->faker->numberBetween(1, 5),
            'analisis' => $this->faker->numberBetween(1, 5),
            'laporan' => $this->faker->numberBetween(1, 5),
            'inovasi' => $this->faker->numberBetween(1, 5),
            'komentar' => $this->faker->sentence,
            'file_pdf' => null, 
        ];
    }
}
