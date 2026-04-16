<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInfoPembayaranToAplikasiTable extends Migration
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
                if (!Schema::hasColumn('aplikasi', 'info_pembayaran')) {
                    $table->longText('info_pembayaran')->nullable()->after('clientKey');
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
                if (Schema::hasColumn('aplikasi', 'info_pembayaran')) {
                    $table->dropColumn('info_pembayaran');
                }
            });
        }
    }
}
