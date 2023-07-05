<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCpaToOffices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->decimal('cpa')->unsigned()->nullable()->default(null);
        });
    }
}
