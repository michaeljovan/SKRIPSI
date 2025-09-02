<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekapkerjasama', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('rekapkerjasama')
                  ->nullOnDelete();
            $table->enum('jenis_permohonan', ['baru', 'perpanjang'])
                  ->default('baru');
            $table->string('no_dokumen')->unique();
            $table->string('unit');
            $table->text('mitra_kerja_sama');
            $table->text('judul_kerja_sama');
            $table->string('bentuk_kerja_sama');
            $table->string('jenis_kerja_sama');
            $table->string('pihak_ukdw');
            $table->string('pihak_mitra');
            $table->string('email_pihak_mitra');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('masa_berlaku');
            $table->string('kategori');
            $table->string('in_kind')->nullable();
            $table->decimal('total_in_kind', 15, 2)->nullable();
            $table->string('in_cash')->nullable();
            $table->decimal('total_in_cash', 15, 2)->nullable();
            $table->integer('jumlah_implementasi')->nullable();
            $table->string('dokumen_path');
            $table->boolean('is_laporan')->default(false);
            $table->boolean('is_kinerja')->default(false);
            $table->boolean('is_mitra')->default(false);
            $table->enum('status', ['aktif', 'selesai', 'dihentikan'])->default('aktif')->index();
            $table->timestamp('stopped_at')->nullable()->index();
            $table->text('stopped_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekapkerjasama');
    }
};
