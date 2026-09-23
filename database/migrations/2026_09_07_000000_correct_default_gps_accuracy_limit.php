<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // The old default was a minimum, incorrectly rejecting more precise fixes.
        // Preserve custom settings; replace only the legacy default.
        DB::table('attendance_settings')->where('max_gps_accuracy', 2)
            ->update(['max_gps_accuracy' => 100]);
    }

    public function down(): void
    {
        // Do not overwrite school settings that may have been edited since migration.
    }
};
