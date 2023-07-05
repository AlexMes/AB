<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueIndexOnRoutes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_order_routes', function (Blueprint $table) {
            $table->unique(['order_id','offer_id','manager_id']);
        });
    }
}
