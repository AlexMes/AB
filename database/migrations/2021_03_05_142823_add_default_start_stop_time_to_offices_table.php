<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultStartStopTimeToOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->string('default_start_at')->nullable();
            $table->string('default_stop_at')->nullable();
        });
    }
}
