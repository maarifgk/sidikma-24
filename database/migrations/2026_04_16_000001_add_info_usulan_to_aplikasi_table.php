<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInfoUsulanToAplikasiTable extends Migration
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
                if (!Schema::hasColumn('aplikasi', 'info_usulan')) {
                    $table->longText('info_usulan')->nullable()->after('info_pembayaran');
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
                if (Schema::hasColumn('aplikasi', 'info_usulan')) {
                    $table->dropColumn('info_usulan');
                }
            });
        }
    }
}
