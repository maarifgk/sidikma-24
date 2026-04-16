<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInfoPersuratanToAplikasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('aplikasi')) {
            Schema::table('aplikasi', function (Blueprint $table) {
                if (!Schema::hasColumn('aplikasi', 'info_persuratan')) {
                    $table->longText('info_persuratan')->nullable()->after('info_aktivasi');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('aplikasi')) {
            Schema::table('aplikasi', function (Blueprint $table) {
                if (Schema::hasColumn('aplikasi', 'info_persuratan')) {
                    $table->dropColumn('info_persuratan');
                }
            });
        }
    }
}
