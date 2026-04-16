<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payment', function (Blueprint $table) {
            if (!Schema::hasColumn('payment', 'installment_group')) {
                $table->string('installment_group')->nullable()->after('pdf_url');
            }
            if (!Schema::hasColumn('payment', 'installment_term')) {
                $table->integer('installment_term')->nullable()->after('installment_group');
            }
            if (!Schema::hasColumn('payment', 'installment_sequence')) {
                $table->integer('installment_sequence')->nullable()->after('installment_term');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment', function (Blueprint $table) {
            if (Schema::hasColumn('payment', 'installment_sequence')) {
                $table->dropColumn('installment_sequence');
            }
            if (Schema::hasColumn('payment', 'installment_term')) {
                $table->dropColumn('installment_term');
            }
            if (Schema::hasColumn('payment', 'installment_group')) {
                $table->dropColumn('installment_group');
            }
        });
    }
};
