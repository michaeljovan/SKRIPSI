<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluasi_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rekap_id');
            $table->string('context', 20)->default('kinerja'); // kinerja | kepuasan
            $table->string('token_hash', 64)->unique();       // sha256 hex, single-use
            $table->timestamp('expires_at');                  // waktu kadaluarsa link
            $table->timestamp('used_at')->nullable();         // kapan dipakai (sekali pakai)
            $table->timestamp('invalidated_at')->nullable();  // dibatalkan manual (opsional)
            $table->string('sent_to_email')->nullable();      // email tujuan saat dikirim
            $table->unsignedBigInteger('created_by_staff_id')->nullable(); // id pengguna pembuat (opsional)
            $table->string('request_ip', 64)->nullable();
            $table->string('user_agent', 191)->nullable();
            $table->timestamps();

            // Relasi ke tabel rekapkerjasama (nama tabel sesuai validasi di kode Anda)
            $table->foreign('rekap_id')
                  ->references('id')->on('rekapkerjasama')
                  ->cascadeOnDelete();

            // Index bantu untuk query
            $table->index(['rekap_id', 'context']);
            $table->index('expires_at');
            $table->index('used_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluasi_links');
    }
};
