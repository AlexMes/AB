<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInitialPlanForDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('initial_plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date')->index();
            $table->unsignedDecimal('leads_amount')->default(0);
            $table->timestamps();
        });
    }
}
