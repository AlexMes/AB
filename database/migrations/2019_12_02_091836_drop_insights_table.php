<?php

use Illuminate\Database\Migrations\Migration;

class DropInsightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('facebook_insights');
    }
}
