<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacebookPlatformInsightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_platform_insights', function (Blueprint $table) {
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
            $table->string('device_platform')->nullable()->default(null)->index();
            $table->string('publisher_platform')->nullable()->default(null)->index();
            $table->string('impression_device')->nullable()->default(null)->index();
            $table->string('platform_position')->nullable()->default(null)->index();

            $table->timestamps();

            $table->unique(['date', 'ad_id', 'device_platform','publisher_platform','impression_device','platform_position']);

            $table->foreign('offer_id')
                ->references('id')
                ->on('offers')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
