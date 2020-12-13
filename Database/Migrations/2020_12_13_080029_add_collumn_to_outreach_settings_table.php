<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCollumnToOutreachSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('outreach_settings', function (Blueprint $table) {
            $table->string('financers')->default('["1"]')->after('observers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('outreach_settings', 'financers')) {
            Schema::table('outreach_settings', function (Blueprint $table) {
                $table->dropColumn('financers');
            });
        }
    }
}
