<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStartMidnightToFacebookAdsets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('facebook_adsets', function (Blueprint $table) {
            $table->boolean('start_midnight')->default(false);
        });
    }
}
