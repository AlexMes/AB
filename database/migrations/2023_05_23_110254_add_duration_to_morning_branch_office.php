<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDurationToMorningBranchOffice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('morning_branch_office', function (Blueprint $table) {
            $table->unsignedSmallInteger('duration')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('morning_branch_office', function (Blueprint $table) {
            $table->dropColumn('duration');
        });
    }
}
