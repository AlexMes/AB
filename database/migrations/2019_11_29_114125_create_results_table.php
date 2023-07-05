<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('results', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->unsignedBigInteger('offer_id')->nullable();
            $table->unsignedBigInteger('office_id')->nullable();
            $table->bigInteger('leads_count')->index()->nullable();
            $table->bigInteger('no_answer_count')->index()->nullable();
            $table->bigInteger('reject_count')->index()->nullable();
            $table->bigInteger('wrong_answer_count')->index()->nullable();
            $table->bigInteger('demo_count')->index()->nullable();
            $table->bigInteger('ftd_count')->index()->nullable();
            $table->timestamps();
        });

        Schema::table('results', function (Blueprint $table) {
            $table->foreign('offer_id')
                ->references('id')
                ->on('offers');
            $table->foreign('office_id')
                ->references('id')
                ->on('offices');
        });
    }
}
