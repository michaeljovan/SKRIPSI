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
        Schema::create('evaluasimitra', function (Blueprint $table) {
            $table->id('idmitra');
            $table->unsignedBigInteger('rekap_id');
            $table->foreign('rekap_id')->references('id')->on('rekapkerjasama')->onDelete('cascade');

            $table->string('nodok');
            $table->string('mitra');

            // Tambahan: siapa yang mengisi dari pihak mitra
            $table->string('pengisi_mitra', 100)->nullable();

            // (1-5) - biarkan string jika memang desain awal kamu string
            $table->string('integritas');
            $table->string('keahlian');
            $table->string('komunikasi');
            $table->string('kerjasamatim');
            $table->string('pengembangandiri');
            $table->string('kreativitas');
            $table->string('bahasaasing');

            $table->text('komentar')->nullable();
            $table->string('file_pdf')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Perbaiki nama tabel agar sesuai dengan yang dibuat di up()
        Schema::dropIfExists('evaluasimitra');
    }
};
