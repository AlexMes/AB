<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacebookCachedAgeInsightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_cached_age_insights', function (Blueprint $table) {
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
            $table->string('link_clicks')->nullable()->default(null);
            $table->json('actions')->nullable()->default(null);
            $table->string('adset_id')->nullable()->default(null)->index();
            $table->string('ad_id')->nullable()->default(null)->index();
            $table->string('age')->index();

            $table->timestamps();

            $table->unique(['date', 'ad_id', 'age']);

            $table->foreign('offer_id')
                ->references('id')
                ->on('offers')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('facebook_cached_age_insights');
    }
}
