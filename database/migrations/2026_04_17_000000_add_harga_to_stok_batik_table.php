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
        Schema::table('stok_batik', function (Blueprint $table) {
            if (!Schema::hasColumn('stok_batik', 'harga')) {
                $table->unsignedInteger('harga')->default(0)->after('stok');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stok_batik', function (Blueprint $table) {
            if (Schema::hasColumn('stok_batik', 'harga')) {
                $table->dropColumn('harga');
            }
        });
    }
};
