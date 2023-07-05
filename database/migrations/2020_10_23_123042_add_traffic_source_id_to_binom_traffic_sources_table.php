<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrafficSourceIdToBinomTrafficSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('binom_traffic_sources', function (Blueprint $table) {
            $table->unsignedBigInteger('traffic_source_id')->index()->nullable()->default(null);

            $table->foreign('traffic_source_id')
                ->on('traffic_sources')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }
}
