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
        Schema::create('rekapkerjasama', function (Blueprint $table) {
            $table->id();
            $table->string('no_dokumen')->unique();
            $table->string('unit');
            $table->text('mitra_kerja_sama');
            $table->text('judul_kerja_sama');
            $table->json('bentuk_kerja_sama'); // Store as JSON array
            $table->string('bentuk_kerja_sama_text')->nullable();
            $table->string('pihak_ukdw');
            $table->string('pihak_mitra');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('masa_berlaku');
            $table->string('kategori');
            $table->text('in_kind')->nullable();
            $table->decimal('total_in_kind', 15, 2)->nullable();
            $table->decimal('in_cash', 15, 2)->nullable();
            $table->decimal('total_in_cash', 15, 2)->nullable();
            $table->integer('jumlah_implementasi')->nullable();
            $table->string('dokumen_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekapkerjasama');
    }
};
