<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDestinationToLeadOrderRoutes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_order_routes', function (Blueprint $table) {
            $table->unsignedBigInteger('destination_id')->nullable()->default(null);

            $table->foreign('destination_id')
                ->references('id')
                ->on('lead_destinations')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }
}
