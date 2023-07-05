<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOfficeIdToDestinations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_destinations', function (Blueprint $table) {
            $table->unsignedBigInteger('office_id')->nullable()->default(null)->index();
        });
    }
}
