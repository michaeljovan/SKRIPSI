<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pelaksanaankerjasama', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('idrekap');
            $table->foreign('idrekap')->references('id')->on('rekapkerjasama')->onDelete('cascade');

            $table->text('ruang_lingkup');

            // kolom baru
            $table->integer('jumlah_dosen_terlibat')->nullable();
            $table->integer('jumlah_mahasiswa_terlibat')->nullable();

            $table->text('dosen_terlibat');
            $table->text('mahasiswa_terlibat');

            $table->text('anggaran_ukdw')->nullable();
            $table->text('hasil_pelaksanaan')->nullable();
            $table->text('tautan_link_kegiatan');
            $table->string('dokumen_kegiatan')->nullable();

            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('pelaksanaankerjasama');
    }
};
