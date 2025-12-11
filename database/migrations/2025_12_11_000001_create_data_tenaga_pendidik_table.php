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
        Schema::create('data_tenaga_pendidik', function (Blueprint $table) {
            $table->id();
            $table->string('tahun_pelajaran');
            $table->unsignedBigInteger('madrasah_id')->nullable();

            // requested columns
            $table->integer('kepala_guru_asn_sertifikasi')->default(0);
            $table->integer('kepala_guru_asn_non_sertifikasi')->default(0);
            $table->integer('kepala_guru_yayasan_sertifikasi_inpassing')->default(0);
            $table->integer('kepala_guru_yayasan_non_sertifikasi')->default(0);

            $table->integer('total')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_tenaga_pendidik');
    }
};
