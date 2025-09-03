<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('evaluasi_mitra_perorangan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rekap_id');
            $table->enum('tipe_responden', ['dosen','mahasiswa']);
            $table->string('nama_responden');
            $table->string('pengisi_mitra', 100);

            // skor 1..5
            $table->tinyInteger('integritas');
            $table->tinyInteger('keahlian');
            $table->tinyInteger('komunikasi');
            $table->tinyInteger('kerjasamatim');
            $table->tinyInteger('pengembangandiri');
            $table->tinyInteger('kreativitas');
            $table->tinyInteger('bahasaasing');

            $table->text('komentar')->nullable();
            $table->string('lampiran_pdf_path')->nullable();
            $table->timestamp('submitted_at')->nullable();

            $table->timestamps();

            // table rekapmu bernama 'rekapkerjasama'
            $table->foreign('rekap_id')->references('id')->on('rekapkerjasama')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluasi_mitra_perorangan');
    }
};
