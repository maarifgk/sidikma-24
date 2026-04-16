<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInfoProposalToAplikasiTable extends Migration
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
                if (!Schema::hasColumn('aplikasi', 'info_proposal')) {
                    $table->longText('info_proposal')->nullable()->after('info_persuratan');
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
                if (Schema::hasColumn('aplikasi', 'info_proposal')) {
                    $table->dropColumn('info_proposal');
                }
            });
        }
    }
}
