<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sk_yayasan_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('sk_template_id')->nullable();
            $table->unsignedSmallInteger('tahun_sk');
            $table->string('original_filename');
            $table->string('stored_filename');
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('source_type', 30)->default('single');
            $table->string('matched_by', 50)->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'tahun_sk']);
            $table->index(['sk_template_id', 'tahun_sk']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sk_yayasan_documents');
    }
};
