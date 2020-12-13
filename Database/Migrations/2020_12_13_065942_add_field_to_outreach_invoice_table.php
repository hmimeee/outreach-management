<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToOutreachInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('outreach_invoices', function (Blueprint $table) {
            $table->boolean('processed')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('outreach_invoices', 'processed')) {
            Schema::table('outreach_invoices', function (Blueprint $table) {
                $table->dropColumn('processed');
            });
        }
    }
}
