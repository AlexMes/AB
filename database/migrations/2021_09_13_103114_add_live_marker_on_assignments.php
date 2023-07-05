<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddLiveMarkerOnAssignments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_order_assignments', function (Blueprint $table) {
            $table->boolean('is_live')->nullable()->default(null);
        });
    }
}
