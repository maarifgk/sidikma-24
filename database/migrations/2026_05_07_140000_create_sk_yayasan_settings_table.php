<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sk_yayasan_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('periode_id')->unique();
            $table->string('nomor_pattern')->default('{{nomor_urut}}/SK.01/LPM.GK/{{periode}}/{{tahun}}');
            $table->unsignedInteger('nomor_awal')->default(1);
            $table->unsignedInteger('nomor_berikutnya')->default(1);
            $table->unsignedSmallInteger('digit_nomor')->default(4);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sk_yayasan_settings');
    }
};
