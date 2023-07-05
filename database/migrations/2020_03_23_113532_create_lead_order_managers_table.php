<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadOrderManagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_order_routes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('manager_id');
            $table->unsignedBigInteger('offer_id');
            $table->unsignedInteger('leadsOrdered')->default(0);
            $table->unsignedInteger('leadsReceived')->default(0);
            $table->dateTime('last_received_at')->nullable()->default(null);
            $table->timestamps();
            $table->softdeletes();

            $table->foreign('order_id')
                ->references('id')
                ->on('leads_orders')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('offer_id')
                ->references('id')
                ->on('offers')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('manager_id')
                ->references('id')
                ->on('managers')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
