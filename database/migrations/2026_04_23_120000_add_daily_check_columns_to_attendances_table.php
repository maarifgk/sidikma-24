<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'check_in_at')) {
                $table->dateTime('check_in_at')->nullable()->after('device_info');
                $table->decimal('check_in_latitude', 10, 7)->nullable()->after('check_in_at');
                $table->decimal('check_in_longitude', 10, 7)->nullable()->after('check_in_latitude');
                $table->decimal('check_in_gps_accuracy', 8, 2)->nullable()->after('check_in_longitude');
                $table->boolean('check_in_is_inside_geofence')->default(false)->after('check_in_gps_accuracy');
                $table->boolean('check_in_is_mock_location')->default(false)->after('check_in_is_inside_geofence');
                $table->string('check_in_mock_detection_source')->nullable()->after('check_in_is_mock_location');
                $table->string('check_in_selfie_path')->nullable()->after('check_in_mock_detection_source');
                $table->string('check_in_rejection_code')->nullable()->after('check_in_selfie_path');
                $table->text('check_in_rejection_reason')->nullable()->after('check_in_rejection_code');
                $table->text('check_in_device_info')->nullable()->after('check_in_rejection_reason');
                $table->dateTime('check_out_at')->nullable()->after('check_in_device_info');
                $table->decimal('check_out_latitude', 10, 7)->nullable()->after('check_out_at');
                $table->decimal('check_out_longitude', 10, 7)->nullable()->after('check_out_latitude');
                $table->decimal('check_out_gps_accuracy', 8, 2)->nullable()->after('check_out_longitude');
                $table->boolean('check_out_is_inside_geofence')->default(false)->after('check_out_gps_accuracy');
                $table->boolean('check_out_is_mock_location')->default(false)->after('check_out_is_inside_geofence');
                $table->string('check_out_mock_detection_source')->nullable()->after('check_out_is_mock_location');
                $table->string('check_out_selfie_path')->nullable()->after('check_out_mock_detection_source');
                $table->string('check_out_rejection_code')->nullable()->after('check_out_selfie_path');
                $table->text('check_out_rejection_reason')->nullable()->after('check_out_rejection_code');
                $table->text('check_out_device_info')->nullable()->after('check_out_rejection_reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (Schema::hasColumn('attendances', 'check_in_at')) {
                $table->dropColumn([
                    'check_in_at',
                    'check_in_latitude',
                    'check_in_longitude',
                    'check_in_gps_accuracy',
                    'check_in_is_inside_geofence',
                    'check_in_is_mock_location',
                    'check_in_mock_detection_source',
                    'check_in_selfie_path',
                    'check_in_rejection_code',
                    'check_in_rejection_reason',
                    'check_in_device_info',
                    'check_out_at',
                    'check_out_latitude',
                    'check_out_longitude',
                    'check_out_gps_accuracy',
                    'check_out_is_inside_geofence',
                    'check_out_is_mock_location',
                    'check_out_mock_detection_source',
                    'check_out_selfie_path',
                    'check_out_rejection_code',
                    'check_out_rejection_reason',
                    'check_out_device_info',
                ]);
            }
        });
    }
};
