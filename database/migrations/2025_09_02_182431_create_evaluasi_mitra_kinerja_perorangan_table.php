<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('evaluasi_mitra_kinerja_perorangan', function (Blueprint $table) {
            $table->id();
            // FK ke tabel rekap
            $table->foreignId('rekap_id')
                ->constrained('rekapkerjasama')
                ->cascadeOnDelete();

            $table->enum('tipe_responden', ['dosen', 'mahasiswa']);
            $table->string('nama_responden', 255);
            $table->string('pengisi_mitra', 100);

            // skor 1..5
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

            $table->string('lainlainlabel', 255)->nullable();
            $table->unsignedTinyInteger('lainlainnilai')->nullable();

            $table->text('komentar')->nullable();
            $table->string('lampiran_pdf_path')->nullable();

            $table->timestamp('submitted_at');
            $table->timestamps();

            $table->index(['rekap_id', 'tipe_responden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluasi_mitra_kinerja_perorangan');
    }
};
