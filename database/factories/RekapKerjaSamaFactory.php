<?php

namespace Database\Factories;

use App\Models\RekapKerjaSama;
use Illuminate\Database\Eloquent\Factories\Factory;

class RekapKerjaSamaFactory extends Factory
{
    protected $model = RekapKerjaSama::class;

    public function definition()
    {
        $start = $this->faker->dateTimeBetween('-2 years', 'now');
        $end = (clone $start)->modify('+1 year');

        return [
            'no_dokumen' => strtoupper('DOC-' . $this->faker->unique()->bothify('###??')),
            'unit' => $this->faker->word(),
            'mitra_kerja_sama' => $this->faker->company(),
            'judul_kerja_sama' => $this->faker->sentence(3),
            'bentuk_kerja_sama' => $this->faker->randomElement(['MoU', 'MoA', 'IA']),
            'jenis_kerja_sama' => $this->faker->randomElement(['Akademik', 'Non-akademik']),
            'pihak_ukdw' => $this->faker->name(),
            'pihak_mitra' => $this->faker->name(),
            'tanggal_mulai' => $start,
            'tanggal_selesai' => $end,
            'masa_berlaku' => $this->faker->numberBetween(1, 5),
            'kategori' => $this->faker->randomElement(['Dalam Negeri', 'Luar Negeri']),
            'in_kind' => $this->faker->randomFloat(2, 0, 10000),
            'total_in_kind' => $this->faker->randomFloat(2, 0, 20000),
            'in_cash' => $this->faker->randomFloat(2, 0, 15000),
            'total_in_cash' => $this->faker->randomFloat(2, 0, 25000),
            'jumlah_implementasi' => $this->faker->numberBetween(0, 10),
            'dokumen_path' => 'files/dokumen_' . $this->faker->unique()->uuid . '.pdf',
            'is_laporan' => $this->faker->boolean(),
            'is_kinerja' => $this->faker->boolean(),
            'is_mitra' => $this->faker->boolean(),
        ];
    }
}
