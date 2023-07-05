<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeManagerIdNullableOnLeadOrderRoutes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_order_routes', function (Blueprint $table) {
            $table->unsignedBigInteger('manager_id')->nullable()->default(null)->change();
        });
    }
}
