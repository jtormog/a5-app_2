<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('ficheroes', 'carpeta_id')) {
            Schema::table('ficheroes', function (Blueprint $table) {
                $table->uuid('carpeta_id')->nullable();
                $table->foreign('carpeta_id')->references('id')->on('carpetas')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('ficheroes', 'carpeta_id')) {
            Schema::table('ficheroes', function (Blueprint $table) {
                $table->dropForeign(['carpeta_id']);
                $table->dropColumn('carpeta_id');
            });
        }
    }
};