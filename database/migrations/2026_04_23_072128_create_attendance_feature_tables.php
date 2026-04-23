<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kelas_id')->unique();
            $table->boolean('enable_check_in')->default(true);
            $table->boolean('enable_check_out')->default(true);
            $table->boolean('enable_permission')->default(true);
            $table->json('geofence_polygon')->nullable();
            $table->time('check_in_time')->default('07:00:00');
            $table->time('check_out_time')->default('14:00:00');
            $table->unsignedSmallInteger('late_tolerance_minutes')->default(10);
            $table->decimal('max_gps_accuracy', 6, 2)->default(3.00);
            $table->boolean('enable_fake_gps_detection')->default(true);
            $table->boolean('require_selfie')->default(false);
            $table->timestamps();

            $table->index('kelas_id');
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('kelas_id')->nullable();
            $table->date('attendance_date');
            $table->enum('check_type', ['datang', 'pulang']);
            $table->enum('status', ['hadir', 'terlambat', 'ditolak'])->default('hadir');
            $table->dateTime('checked_at');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('gps_accuracy', 8, 2);
            $table->boolean('is_inside_geofence')->default(false);
            $table->boolean('is_mock_location')->default(false);
            $table->string('mock_detection_source')->nullable();
            $table->string('selfie_path')->nullable();
            $table->string('rejection_code')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('device_info')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'attendance_date']);
            $table->index(['kelas_id', 'attendance_date', 'status']);
            $table->index(['check_type', 'status']);
        });

        Schema::create('attendance_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('kelas_id')->nullable();
            $table->enum('category', ['terlambat', 'sakit', 'tidak_masuk', 'tugas_dinas', 'cuti']);
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason')->nullable();
            $table->string('attachment_path')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('reviewer_id')->nullable();
            $table->dateTime('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'start_date', 'end_date']);
            $table->index(['kelas_id', 'status']);
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_permissions');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('attendance_settings');
    }
};
