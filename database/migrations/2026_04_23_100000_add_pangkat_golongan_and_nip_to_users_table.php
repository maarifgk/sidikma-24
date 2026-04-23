<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'nip')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('nip')->nullable()->after('nuptk');
            });
        }

        if (!Schema::hasColumn('users', 'pangkat_golongan')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('pangkat_golongan')->nullable()->after('nip');
            });
        }
    }

    public function down(): void
    {
        $columns = [];

        if (Schema::hasColumn('users', 'pangkat_golongan')) {
            $columns[] = 'pangkat_golongan';
        }

        if (Schema::hasColumn('users', 'nip')) {
            $columns[] = 'nip';
        }

        if (!empty($columns)) {
            Schema::table('users', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
