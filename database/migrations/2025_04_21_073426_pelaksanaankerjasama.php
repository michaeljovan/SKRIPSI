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
            $table->foreignId('idrekap')->references('id')->on('rekapkerjasama');
            $table->text('ruang_lingkup');
            $table->string('dosen_terlibat');
            $table->string('mahasiswa_terlibat');
            $table->text('anggaran_ukdw')->nullable();
            $table->text('hasil_pelaksanaan')->nullable();
            $table->text('tautan_link_kegiatan');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
