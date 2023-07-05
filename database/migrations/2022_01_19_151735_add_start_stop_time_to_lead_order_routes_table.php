<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStartStopTimeToLeadOrderRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_order_routes', function (Blueprint $table) {
            $table->string('start_at')->nullable()->default(null);
            $table->string('stop_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lead_order_routes', function (Blueprint $table) {
            $table->dropColumn('start_at');
            $table->dropColumn('stop_at');
        });
    }
}
