<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date')->index();
            $table->unsignedBigInteger('office_id')->index();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['date','office_id']);
            $table->foreign('office_id')
                ->references('id')
                ->on('offices')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
