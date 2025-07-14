<?php

namespace Database\Factories;
use App\Models\RekapKerjaSama;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PelaksanaanKerjaSama>
 */
class PelaksanaanKerjaSamaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'idrekap' => RekapKerjaSama::factory(),
            'ruang_lingkup' => $this->faker->sentence(8),
            'dosen_terlibat' => $this->faker->name(),
            'mahasiswa_terlibat' => $this->faker->name(),
            'anggaran_ukdw' => $this->faker->numberBetween(1000000, 10000000),
            'hasil_pelaksanaan' => $this->faker->paragraph(3),
            'tautan_link_kegiatan' => $this->faker->url(),
        ];
    }
}
