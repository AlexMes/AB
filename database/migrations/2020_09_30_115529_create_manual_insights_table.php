<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManualInsightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_insights', function (Blueprint $table) {
            $table->id('id');
            $table->date('date')->index();
            $table->string('account_id')->index();
            $table->string('campaign_id')->index();
            $table->unsignedInteger('impressions')->default(0);
            $table->unsignedInteger('clicks')->default(0);
            $table->unsignedDecimal('spend')->default(0);
            $table->unsignedInteger('leads_cnt')->default(0);
            $table->timestamps();

            $table->unique(['date', 'account_id', 'campaign_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manual_insights');
    }
}
