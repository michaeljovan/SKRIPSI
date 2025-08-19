<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_evaluasi_kinerja_otps_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('evaluasi_kinerja_otps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rekap_id');
            $table->unsignedBigInteger('staff_id')->nullable();
            $table->string('code_hash');             // simpan hash OTP, bukan plain
            $table->dateTime('expires_at');
            $table->dateTime('used_at')->nullable();
            $table->string('sent_to_email');         // email staff yg dikirimi
            $table->string('request_ip')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('rekap_id')->references('id')->on('rekapkerjasama')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('evaluasi_kinerja_otps');
    }
};

