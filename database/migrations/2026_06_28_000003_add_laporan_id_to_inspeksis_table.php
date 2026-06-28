<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inspeksis', function (Blueprint $table) {
            $table->foreignId('laporan_id')->nullable()->after('petugas_id')->constrained('laporans')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inspeksis', function (Blueprint $table) {
            $table->dropForeign(['laporan_id']);
            $table->dropColumn('laporan_id');
        });
    }
};
