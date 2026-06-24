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
        $backup = [];
        foreach (DB::table('fasilitas')->whereNotNull('penanggung_jawab')->where('penanggung_jawab', '!=', '')->cursor() as $f) {
            $backup[$f->id] = (int) $f->penanggung_jawab;
        }

        Schema::table('fasilitas', function (Blueprint $table) {
            $table->dropColumn('penanggung_jawab');
        });

        Schema::table('fasilitas', function (Blueprint $table) {
            $table->unsignedBigInteger('penanggung_jawab')->nullable();
            $table->foreign('penanggung_jawab')->references('id')->on('users')->onDelete('set null');
        });

        foreach ($backup as $id => $pj) {
            DB::table('fasilitas')->where('id', $id)->update(['penanggung_jawab' => $pj]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fasilitas', function (Blueprint $table) {
            $table->dropForeign(['penanggung_jawab']);
            $table->dropColumn('penanggung_jawab');
        });

        Schema::table('fasilitas', function (Blueprint $table) {
            $table->string('penanggung_jawab')->nullable();
        });
    }
};
