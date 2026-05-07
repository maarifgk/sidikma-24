<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sk_yayasan_settings', function (Blueprint $table) {
            $table->string('nomor_text')->default('SK.01/LPM.GK')->after('periode_id');
        });
    }

    public function down(): void
    {
        Schema::table('sk_yayasan_settings', function (Blueprint $table) {
            $table->dropColumn('nomor_text');
        });
    }
};
