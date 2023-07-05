<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AttachHostersToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('hosting_id')->nullable()->default(null);

            $table->foreign('hosting_id')
                ->references('id')
                ->on('hostings')
                ->onUpdate('set null')
                ->onDelete('set null');
        });
    }
}
