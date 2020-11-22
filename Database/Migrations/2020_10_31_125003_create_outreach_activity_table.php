<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutreachActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outreach_activities', function (Blueprint $table) {
           $table->bigIncrements('id');

           $table->unsignedInteger('user_id')->nullable();
           $table->foreign('user_id')->references('id')->on('users');

           $table->unsignedBigInteger('outreach_site_id')->nullable();
           $table->foreign('outreach_site_id')->references('id')->on('outreach_sites')->onDelete('cascade');

           $table->unsignedBigInteger('outreach_backlink_id')->nullable();
           $table->foreign('outreach_backlink_id')->references('id')->on('outreach_backlinks')->onDelete('cascade');

           $table->unsignedBigInteger('outreach_invoice_id')->nullable();
           $table->foreign('outreach_invoice_id')->references('id')->on('outreach_invoices')->onDelete('cascade');

           $table->string('type');
           $table->string('message');

           $table->timestamps();
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('outreach_activities');
    }
}
