<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutreachSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outreach_sites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('website')->unique();
            $table->string('niche');
            $table->string('domain_rating')->nullable();
            $table->string('traffic')->nullable();
            $table->string('traffic_value')->nullable();
            $table->string('spam_score')->nullable();
            $table->decimal('post_price')->nullable()->default(0);
            $table->decimal('link_price')->nullable()->default(0);
            $table->string('ahref_link')->nullable();
            $table->string('ahref_snap')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'soft rejected'])->default('pending');
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
        Schema::dropIfExists('outreach_sites');
    }
}
