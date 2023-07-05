<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIgnorePausedRoutesToResellBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resell_batches', function (Blueprint $table) {
            $table->boolean('ignore_paused_routes')->default(false);
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
            $table->dropColumn('ignore_paused_routes');
        });
    }
}
