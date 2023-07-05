<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreateOfferAndSimulateAutologinToResellBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resell_batches', function (Blueprint $table) {
            $table->boolean('create_offer')->default(false);
            $table->boolean('simulate_autologin')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('resell_batches', function (Blueprint $table) {
            $table->dropColumn('create_offer');
            $table->dropColumn('simulate_autologin');
        });
    }
}
