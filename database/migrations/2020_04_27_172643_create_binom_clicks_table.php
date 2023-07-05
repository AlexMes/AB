<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBinomClicksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('binom_clicks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('clickid')->index();
            $table->unsignedBigInteger('lead_id')->unique();
            $table->dateTime('click_time')->nullable()->default(null);
            $table->boolean('lp_click')->default(false);
            $table->boolean('unique')->default(false);
            $table->boolean('conversion');
            $table->dateTime('conversion_time')->nullable()->default(null);
            $table->unsignedBigInteger('campaign_id')->nullable()->default(null);
            $table->string('campaign_name')->nullable()->default(null);
            $table->unsignedBigInteger('path_id')->nullable()->default(null);
            $table->text('offer_name')->nullable()->default(null);
            $table->unsignedBigInteger('landing_id')->nullable()->default(null);
            $table->string('landing_name')->nullable()->default(null);
            $table->string('ip')->nullable()->default(null);
            $table->string('country_name')->nullable()->default(null);
            $table->string('country_code')->nullable()->default(null);
            $table->string('city_name')->nullable()->default(null);
            $table->string('isp_name')->nullable()->default(null);
            $table->string('connection_type')->nullable()->default(null);
            $table->text('user_agent')->nullable()->default(null);
            $table->string('browser')->nullable()->default(null);
            $table->string('browser_version')->nullable()->default(null);
            $table->string('device_brand')->nullable()->default(null);
            $table->string('device_model')->nullable()->default(null);
            $table->string('device_name')->nullable()->default(null);
            $table->string('display_size')->nullable()->default(null);
            $table->string('display_resolution')->nullable()->default(null);
            $table->string('device_pointing_method')->nullable()->default(null);
            $table->string('os_name')->nullable()->default(null);
            $table->string('os_version')->nullable()->default(null);
            $table->text('referer_url')->nullable()->default(null);
            $table->string('referer_domain')->nullable()->default(null);
            $table->string('language')->nullable()->default(null);
            $table->string('token_1_value')->nullable()->default(null);
            $table->string('token_2_value')->nullable()->default(null);
            $table->string('token_3_value')->nullable()->default(null);
            $table->string('token_4_value')->nullable()->default(null);
            $table->string('token_5_value')->nullable()->default(null);
            $table->string('token_6_value')->nullable()->default(null);
            $table->string('token_7_value')->nullable()->default(null);
            $table->string('token_8_value')->nullable()->default(null);
            $table->string('token_9_value')->nullable()->default(null);
            $table->string('token_10_value')->nullable()->default(null);
            $table->timestamps();
        });
    }
}
