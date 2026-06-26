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
        Schema::create('fasilitas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_fasilitas');
            $table->string('jenis_fasilitas')->index();
            $table->string('lokasi');
            $table->unsignedBigInteger('penanggung_jawab')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->string('foto_before')->nullable();
            $table->string('foto_after')->nullable();
            $table->timestamps();

            $table->foreign('penanggung_jawab')->references('id')->on('users')->onDelete('set null');
            $table->foreign('jenis_fasilitas')->references('slug')->on('jenis_fasilitas')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fasilitas');
    }
};
