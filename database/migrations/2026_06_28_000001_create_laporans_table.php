<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelapor');
            $table->string('no_telp');
            $table->foreignId('fasilitas_id')->constrained('fasilitas')->onDelete('cascade');
            $table->text('keluhan');
            $table->string('foto_bukti')->nullable();
            $table->enum('status', ['pending', 'diproses', 'selesai', 'ditolak'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporans');
    }
};
