<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutreachInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outreach_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->decimal('amount');
            $table->string('seller')->nullable();
            $table->unsignedBigInteger('outreach_site_id')->nullable();
            $table->foreign('outreach_site_id')->references('id')->on('outreach_sites')->onDelete('restrict');
            $table->string('payment_method');
            $table->text('payment_details');
            $table->unsignedInteger('reviewer_id')->nullable();
            $table->foreign('reviewer_id')->references('id')->on('users');
            $table->date('payment_date')->nullable();
            $table->boolean('status')->default(false);
            $table->boolean('review')->default(false);
            $table->text('receipt')->nullable();
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
        Schema::dropIfExists('outreach_invoices');
    }
}
