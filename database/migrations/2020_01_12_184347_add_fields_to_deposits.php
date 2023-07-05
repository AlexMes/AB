<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToDeposits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->string('city')->nullable()->default(null)->index();
            $table->string('profession')->nullable()->default(null)->index();
        });
    }
}
