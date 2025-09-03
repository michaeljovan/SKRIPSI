<?php

namespace Database\Seeders;

use App\Models\RekapKerjaSama;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Schema;

class RekapKerjaSamaSeeder extends Seeder
{
    public function run(): void
    {
        try {
            // Nonaktifkan foreign key checks sementara
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Hapus data dari tabel terkait jika tabelnya ada
            $this->safeDelete('pelaksanaankerjasama');
            $this->safeDelete('evaluasi_mitra_kinerja');
            $this->safeDelete('evaluasi_mitra');

            // Hapus data rekap
            RekapKerjaSama::truncate();

            // Aktifkan kembali foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Buat direktori penyimpanan jika belum ada
            if (!Storage::exists('public/dokumen_kerja_sama')) {
                Storage::makeDirectory('public/dokumen_kerja_sama');
            }

            $faker = Faker::create('id_ID');

            // Data acak untuk berbagai bidang
            $units = ['Fakultas Teknologi Informasi', 'Informatika', 'Sistem Informasi'];
            $bentukKerjaSama = [
                ['Penelitian'],
                ['Pendidikan'],
                ['Pengabdian'],
                ['Penelitian', 'Pendidikan'],
                ['Pendidikan', 'Pengabdian'],
                ['Penelitian', 'Pengabdian'],
                ['Penelitian', 'Pendidikan', 'Pengabdian']
            ];
            $jenisKerjaSama = ['MoU', 'MoA', 'IA'];
            $kategori = ['nasional', 'internasional', 'lokal'];
            $perusahaan = ['PT.', 'CV.', 'UD.', 'PD.'];
            $bidangUsaha = [
                'Teknologi',
                'Pendidikan',
                'Kesehatan',
                'Keuangan',
                'Manufaktur',
                'Retail',
                'Jasa',
                'Pertanian'
            ];

            // Generate 50 data dummy
            for ($i = 1; $i <= 50; $i++) {
                $startDate = $faker->dateTimeBetween('-2 years', 'now');
                $endDate = $faker->dateTimeBetween($startDate, '+3 years');
                $duration = $endDate->diff($startDate)->days + 1;

                $bentuk = $faker->randomElement($bentukKerjaSama);
                $mitra = $faker->randomElement($perusahaan) . ' ' . $faker->company . ' ' . $faker->randomElement($bidangUsaha);

                $inKind = $faker->boolean(60) ? $faker->numberBetween(5000000, 500000000) : null;
                $inCash = $faker->boolean(60) ? $faker->numberBetween(5000000, 500000000) : null;

                RekapKerjaSama::create([
                    'no_dokumen' => 'KS/FTI/' . date('Y') . '/' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'unit' => $faker->randomElement($units),
                    'mitra_kerja_sama' => $mitra,
                    'judul_kerja_sama' => 'Kerja Sama ' . implode(' dan ', $bentuk) . ' dengan ' . $mitra,
                    'bentuk_kerja_sama' => implode(', ', $bentuk),
                    'jenis_kerja_sama' => $faker->randomElement($jenisKerjaSama),
                    'pihak_ukdw' => $faker->name,
                    'pihak_mitra' => $faker->name,
                    'email_pihak_mitra' => $faker->safeEmail,
                    'tanggal_mulai' => $startDate,
                    'tanggal_selesai' => $endDate,
                    'masa_berlaku' => $duration,
                    'kategori' => $faker->randomElement($kategori),
                    'in_kind' => $inKind,
                    'total_in_kind' => $inKind ? $faker->numberBetween(10000000, 200000000) : null,
                    'in_cash' => $inCash,
                    'total_in_cash' => $inCash,
                    'jumlah_implementasi' => $faker->numberBetween(0, 10),
                    'dokumen_path' => 'dokumen_kerja_sama/dummy_' . $i . '.pdf',
                    'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                    'updated_at' => $faker->dateTimeBetween('-1 year', 'now'),
                ]);
            }

            $this->command->info('Berhasil menambahkan 50 data dummy Rekap Kerja Sama!');
        } catch (\Exception $e) {
            $this->command->error('Error: ' . $e->getMessage());
        }
    }

    /**
     * Fungsi untuk menghapus data dari tabel hanya jika tabel tersebut ada
     */
    protected function safeDelete(string $tableName): void
    {
        try {
            if (Schema::hasTable($tableName)) {
                DB::table($tableName)->delete();
                $this->command->info("Data dari tabel {$tableName} berhasil dihapus");
            } else {
                $this->command->warn("Tabel {$tableName} tidak ditemukan, dilewati");
            }
        } catch (\Exception $e) {
            $this->command->error("Gagal menghapus data dari {$tableName}: " . $e->getMessage());
        }
    }
}
