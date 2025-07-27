<?php

namespace Database\Factories;

use App\Models\RekapKerjaSama;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

class RekapKerjaSamaFactory extends Factory
{
    protected $model = RekapKerjaSama::class;

    public function definition()
    {
        $start = $this->faker->dateTimeBetween('-2 years', 'now');
        $end = (clone $start)->modify('+1 year');

        return [
            'parent_id' => null,
            'no_dokumen_induk' => null,
            'no_dokumen' => strtoupper('DOC-' . $this->faker->unique()->bothify('###??')),
            'unit' => $this->faker->randomElement(['Fakultas Teknologi Informasi', 'Informatika', 'Sistem Informasi']),
            'mitra_kerja_sama' => $this->faker->company(),
            'judul_kerja_sama' => $this->faker->sentence(3),
            'bentuk_kerja_sama' => $this->faker->randomElement(['MoU', 'MoA', 'IA']), // valid array
            'jenis_kerja_sama' => $this->faker->randomElement(['Pendidikan', 'Penelitian', 'Pengabdian']), // valid enum
            'pihak_ukdw' => $this->faker->company(),
            'pihak_mitra' => $this->faker->company(),
            'tanggal_mulai' => $start->format('Y-m-d'),
            'tanggal_selesai' => $end->format('Y-m-d'),
            'masa_berlaku' => $this->faker->numberBetween(1, 5),
            'kategori' => $this->faker->randomElement(['Nasional', 'Internasional']), // valid enum
            'in_kind' => $this->faker->randomFloat(2, 0, 10000),
            'total_in_kind' => $this->faker->randomFloat(2, 0, 20000),
            'in_cash' => $this->faker->randomFloat(2, 0, 15000),
            'total_in_cash' => $this->faker->randomFloat(2, 0, 25000),
            'jumlah_implementasi' => $this->faker->numberBetween(0, 10),
            'dokumen_path' => 'dokumen/' . $this->faker->unique()->uuid . '.pdf',
            'is_laporan' => $this->faker->boolean(),
            'is_kinerja' => $this->faker->boolean(),
            'is_mitra' => $this->faker->boolean(),
        ];
    }

    public function withPdf(string $fileName = null)
    {
        return $this->afterCreating(function (RekapKerjaSama $rekap) use ($fileName) {
            $fileName = $fileName ?? 'dummy_' . uniqid() . '.pdf';
            $filePath = 'dokumen/' . $fileName;

            Storage::disk('public')->put($filePath, 'Isi dummy file PDF');

            $rekap->update([
                'dokumen_path' => $filePath,
            ]);
        });
    }
}
