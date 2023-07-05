<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrafficSourceIdToAffiliatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('affiliates', function (Blueprint $table) {
            $table->unsignedBigInteger('traffic_source_id')->nullable()->default(null);

            $table->foreign('traffic_source_id')
                ->references('id')
                ->on('traffic_sources')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }
}
