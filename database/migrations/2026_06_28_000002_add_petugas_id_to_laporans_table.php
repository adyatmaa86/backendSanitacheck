<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            $table->foreignId('petugas_id')->nullable()->after('fasilitas_id')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            $table->dropForeign(['petugas_id']);
            $table->dropColumn('petugas_id');
        });
    }
};
