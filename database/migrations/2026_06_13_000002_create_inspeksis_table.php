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
        Schema::create('inspeksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fasilitas_id')->constrained('fasilitas')->onDelete('cascade');
            $table->foreignId('petugas_id')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('tanggal_inspeksi')->index();
            $table->string('kondisi_kebersihan'); // baik, cukup, buruk
            $table->string('ketersediaan_air'); // tersedia, tidak
            $table->string('ketersediaan_sabun'); // tersedia, tidak
            $table->string('bau_tidak_sedap'); // ya, tidak
            $table->text('catatan')->nullable();
            $table->string('foto')->nullable();
            $table->string('foto_selesai')->nullable();
            $table->string('status_tindak_lanjut'); // aman, perlu dibersihkan, perlu perbaikan
            $table->boolean('is_completed')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspeksis');
    }
};
