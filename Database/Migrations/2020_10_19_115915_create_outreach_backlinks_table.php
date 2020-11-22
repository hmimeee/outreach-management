<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutreachBacklinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outreach_backlinks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('project_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('restrict');
            $table->unsignedBigInteger('outreach_site_id')->nullable();
            $table->foreign('outreach_site_id')->references('id')->on('outreach_sites')->onDelete('cascade');
            $table->string('website', 255)->nullable();
            $table->string('backlink', 255)->unique();
            $table->enum('type', ['post', 'link'])->default('post');
            $table->string('url', 255);
            $table->date('published_date');
            $table->boolean('indexed')->default(false);
            $table->boolean('paid')->default(false);
            $table->decimal('cost');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('remarks')->nullable();
            $table->unsignedInteger('outreach_invoice_id')->nullable();
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
        Schema::dropIfExists('outreach_backlinks');
    }
}
