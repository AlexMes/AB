<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignAdsetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_adsets', function (Blueprint $table) {
            $table->string('id')->index();
            $table->string('name')->nullable()->default(null);
            $table->string('account_id')->index();
            $table->string('campaign_id')->index();
            $table->string('budget_remaining')->nullable()->default(null);
            $table->string('daily_budget')->nullable()->default(null);
            $table->string('lifetime_budget')->nullable()->default(null);
            $table->string('status')->nullable()->default(null);
            $table->string('configured_status')->nullable()->default(null);
            $table->string('effective_status')->nullable()->default(null);
            $table->json('issues_info')->nullable()->default(null);
            $table->json('targeting')->nullable()->default(null);
            $table->string('destination_type')->nullable()->default(null);
            $table->json('recommendations')->nullable()->default(null);
            $table->string('billing_event')->nullable()->default(null);

            $table->timestamps();
        });
    }
}
