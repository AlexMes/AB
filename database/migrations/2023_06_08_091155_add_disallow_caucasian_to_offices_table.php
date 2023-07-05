<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDisallowCaucasianToOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->boolean('disallow_caucasian')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->dropColumn('disallow_caucasian');
        });
    }
}
