<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnTrafficSourceIdToManualBundlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manual_bundles', function (Blueprint $table) {
            $table->unsignedBigInteger('traffic_source_id')->index()->nullable();

            $table->foreign('traffic_source_id')
                ->on('manual_traffic_sources')
                ->references('id')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('manual_bundles', function (Blueprint $table) {
            $table->dropColumn('traffic_source_id');
        });
    }
}
