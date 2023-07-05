<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacebookCachedInsightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_cached_insights', function (Blueprint $table) {
            $table->date('date')->index();
            $table->string('account_id')->index();
            $table->string('campaign_id')->index();
            $table->unsignedBigInteger('offer_id')->nullable()->default(null)->index();
            $table->unsignedBigInteger('reach');
            $table->unsignedBigInteger('impressions');
            $table->string('spend');
            $table->string('cpm');
            $table->string('cpc');
            $table->string('ctr');
            $table->string('cpl');
            $table->unsignedBigInteger('clicks');
            $table->unsignedBigInteger('leads_cnt');

            $table->timestamps();

            $table->foreign('account_id')
                ->references('account_id')
                ->on('facebook_ads_accounts')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('offer_id')
                ->references('id')
                ->on('offers')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
