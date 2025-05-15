<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RekapKerjaSamaSeeder extends Seeder
{
    public function run()
    {
        $units = [
            'Fakultas Teknologi Informasi',
            'Informatika',
            'Sistem Informasi',
        ];

        $mitras = [
            'PT. Teknologi Maju Indonesia',
            'CV. Solusi Digital',
            'Universitas Gadjah Mada',
            'Google Indonesia',
            'Microsoft Corporation',
            'PT. Telkom Indonesia',
            'Dinas Pendidikan Provinsi Jawa Tengah',
            'PT. Bank Central Asia',
            'PT. Astra International',
            'Universitas Indonesia'
        ];

        $bentukKerjaSama = [
            ['MoU'],
            ['MoA'],
            ['Implementasi'],
        ];

        $kategoris = [
            'Pendidikan',
            'Penelitian',
            'Pengabdian Masyarakat',
            'Magang',
            'Beasiswa'
        ];

        $data = [];

        for ($i = 1; $i <= 50; $i++) {
            $startDate = Carbon::now()->subDays(rand(1, 365));
            $endDate = (clone $startDate)->addDays(rand(30, 730));
            $duration = $endDate->diffInDays($startDate);

            $bentuk = $bentukKerjaSama[array_rand($bentukKerjaSama)];

            $data[] = [
                'no_dokumen' => 'DOC-' . str_pad($i, 4, '0', STR_PAD_LEFT) . '-' . date('Y'),
                'unit' => $units[array_rand($units)],
                'mitra_kerja_sama' => $mitras[array_rand($mitras)],
                'judul_kerja_sama' => 'Kerja Sama ' . $kategoris[array_rand($kategoris)] . ' dengan ' . $mitras[array_rand($mitras)],
                'bentuk_kerja_sama' => json_encode($bentuk),
                'bentuk_kerja_sama_text' => in_array('Implementasi', $bentuk) ? 'Implementasi khusus bidang TI' : null,
                'pihak_ukdw' => 'Rektor UKDW',
                'pihak_mitra' => 'Direktur ' . $mitras[array_rand($mitras)],
                'tanggal_mulai' => $startDate,
                'tanggal_selesai' => $endDate,
                'masa_berlaku' => $duration,
                'kategori' => $kategoris[array_rand($kategoris)],
                'in_kind' => rand(0, 1) ? 'Perangkat lunak dan pelatihan' : null,
                'total_in_kind' => rand(0, 1) ? rand(5000000, 50000000) : null,
                'in_cash' => rand(0, 1) ? rand(10000000, 100000000) : null,
                'total_in_cash' => rand(0, 1) ? rand(50000000, 500000000) : null,
                'jumlah_implementasi' => rand(0, 5),
                'dokumen_path' => 'dokumen_kerja_sama/contoh_dokumen_' . $i . '.pdf',
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        DB::table('rekapkerjasama')->insert($data);
    }
}
