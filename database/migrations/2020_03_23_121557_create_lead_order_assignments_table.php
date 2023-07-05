<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadOrderAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_order_assignments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('route_id')->index();
            $table->unsignedBigInteger('lead_id');
            $table->timestamps();

            $table->foreign('route_id')
                ->references('id')
                ->on('lead_order_routes')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('lead_id')
                ->references('id')
                ->on('leads')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
