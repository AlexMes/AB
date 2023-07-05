<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBinomStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('binom_statistics', function (Blueprint $table) {
            $table->date('date');
            $table->unsignedBigInteger('campaign_id')->index();
            $table->unsignedBigInteger('clicks');
            $table->unsignedBigInteger('lp_clicks');
            $table->unsignedBigInteger('lp_views');
            $table->unsignedBigInteger('unique_clicks');
            $table->unsignedBigInteger('unique_camp_clicks');
            $table->unsignedBigInteger('leads');

            $table->timestamps();

            $table->unique(['date','campaign_id']);
        });
    }
}
