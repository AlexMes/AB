<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddManagerMarkToDeposits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->string('manager')->nullable()->default(null)->index();
        });
    }
}
