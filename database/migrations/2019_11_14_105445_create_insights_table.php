<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInsightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_insights', function (Blueprint $table) {
            $table->string('id');
            $table->date('effective_date');
            $table->string('account_id');
            $table->string('campaign_id')->index();
            $table->string('adset_id')->nullable()->default(null);
            $table->json('adlabels')->nullable()->default(null);
            $table->unsignedBigInteger('clicks');
            $table->string('cpc')->nullable()->default(null);
            $table->string('cpp')->nullable()->default(null);
            $table->string('cpm')->nullable()->default(null);
            $table->string('spend')->nullable()->default(null);
            $table->string('ctr')->nullable()->default(null);
            $table->string('unique_clicks')->nullable()->default(null);
            $table->string('unique_ctr')->nullable()->default(null);
            $table->string('impressions')->nullable()->default(null);

            $table->string("bid_adjustments")->nullable()->default(null);
            $table->string("bid_amount")->nullable()->default(null);
            $table->string("bid_constraints")->nullable()->default(null);
            $table->string("bid_info")->nullable()->default(null);
            $table->string("bid_strategy")->nullable()->default(null);
            $table->string("budget_remaining")->nullable()->default(null);
            $table->string("configured_status")->nullable()->default(null);
            $table->string("daily_budget")->nullable()->default(null);
            $table->string("daily_min_spend_target")->nullable()->default(null);
            $table->string("effective_status")->nullable()->default(null);
            $table->string("status")->nullable()->default(null);
            $table->dateTime("created_time")->nullable()->default(null);
            $table->dateTime("start_time")->nullable()->default(null);
            $table->dateTime("updated_time")->nullable()->default(null);
            $table->dateTime("time_start")->nullable()->default(null);
            $table->dateTime("time_stop")->nullable()->default(null);
            $table->timestamps();
        });
    }
}
