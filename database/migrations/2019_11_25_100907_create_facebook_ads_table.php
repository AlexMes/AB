<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacebookAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_ads', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');

            $table->string('account_id')->index();
            $table->string('campaign_id')->index();
            $table->string('adset_id')->index();

            $table->string('status')->nullable()->default(null);
            $table->string('effective_status')->nullable()->default(null);
            $table->string('configured_status')->nullable()->default(null);

            $table->json('ad_review_feedback')->nullable()->default(null);
            $table->json('targeting')->nullable()->default(null);
            $table->json('recommendations')->nullable()->default(null);
            $table->json('issues_info')->nullable()->default(null);
            $table->timestamps();
        });
    }
}
