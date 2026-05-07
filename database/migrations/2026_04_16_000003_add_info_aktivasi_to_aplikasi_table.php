<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInfoAktivasiToAplikasiTable extends Migration
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
                if (!Schema::hasColumn('aplikasi', 'info_aktivasi')) {
                    $table->longText('info_aktivasi')->nullable()->after('info_usulan');
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
                if (Schema::hasColumn('aplikasi', 'info_aktivasi')) {
                    $table->dropColumn('info_aktivasi');
                }
            });
        }
    }
}
