<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLiveIntervalToLeadsOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads_orders', function (Blueprint $table) {
            $table->unsignedInteger('live_interval')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads_orders', function (Blueprint $table) {
            $table->dropColumn('live_interval');
        });
    }
}
