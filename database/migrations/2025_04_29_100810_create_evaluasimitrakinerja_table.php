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
        Schema::create('evaluasimitrakinerja', function (Blueprint $table) {
            $table->id('idkinerja');
            $table->unsignedBigInteger('rekap_id');
            $table->foreign('rekap_id')->references('id')->on('rekapkerjasama')->onDelete('cascade');
            $table->string('nodok');
            $table->string('mitra');

            // (1-5)
            $table->string('integritas');
            $table->string('keahlian');
            $table->string('komunikasi');
            $table->string('kerjasamatim');
            $table->string('pengembangandiri');
            $table->string('kreativitas');
            $table->string('bahasaasing');
            $table->string('teknologi');
            $table->string('manajerial');
            $table->string('analisis');
            $table->string('laporan');
            $table->string('inovasi');
            $table->string('lainlainlabel')->nullable();
            $table->string('lainlainnilai')->nullable();

            $table->text('komentar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluasimitrakinerja');
    }
};
