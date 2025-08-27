<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluasimitrakinerja', function (Blueprint $table) {
            $table->id('idkinerja');

            $table->foreignId('rekap_id')
                  ->constrained('rekapkerjasama')
                  ->cascadeOnDelete();

            $table->string('nodok');
            $table->string('mitra');

            // nilai 1..5
            $table->unsignedTinyInteger('integritas');
            $table->unsignedTinyInteger('keahlian');
            $table->unsignedTinyInteger('komunikasi');
            $table->unsignedTinyInteger('kerjasamatim');
            $table->unsignedTinyInteger('pengembangandiri');
            $table->unsignedTinyInteger('kreativitas');
            $table->unsignedTinyInteger('bahasaasing');
            $table->unsignedTinyInteger('teknologi');
            $table->unsignedTinyInteger('manajerial');
            $table->unsignedTinyInteger('analisis');
            $table->unsignedTinyInteger('laporan');
            $table->unsignedTinyInteger('inovasi');

            $table->string('lainlainlabel')->nullable();
            $table->unsignedTinyInteger('lainlainnilai')->nullable();

            $table->string('pengisi_mitra', 100)->nullable(); // <— tambahan

            $table->text('komentar')->nullable();
            $table->string('file_pdf')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluasimitrakinerja');
    }
};
