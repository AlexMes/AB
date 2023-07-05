<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUnusedFieldsFromDeposits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn('city');
        });

        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn('profession');
        });

        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn('sex');
        });

        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn('age');
        });

        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn('manager');
        });
    }
}
