<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AllowNullsInProgressColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_order_routes', function (Blueprint $table) {
            $table->unsignedDecimal('progress', 6, 2)->nullable()->change();
        });
    }
}
