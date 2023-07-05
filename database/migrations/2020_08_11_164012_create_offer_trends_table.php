<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfferTrendsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_statistics_log', function (Blueprint $table) {
            $table->id();
            $table->dateTime('datetime');
            $table->unsignedBigInteger('offer_id')->index();
            $table->unsignedBigInteger('impressions')->nullable()->default(null);
            $table->unsignedBigInteger('clicks')->nullable()->default(null);
            $table->unsignedBigInteger('accounts')->nullable()->default(null);
            $table->decimal('cpm')->nullable()->default(null);
            $table->decimal('cpc')->nullable()->default(null);
            $table->decimal('ctr')->nullable()->default(null);
            $table->decimal('cr')->nullable()->default(null);
            $table->bigInteger('fb_leads')->nullable()->default(null);
            $table->decimal('fb_cpl')->nullable()->default(null);
            $table->unsignedBigInteger('binom_clicks')->nullable()->default(null);
            $table->unsignedBigInteger('binom_unique_clicks')->nullable()->default(null);
            $table->unsignedBigInteger('lp_views')->nullable()->default(null);
            $table->unsignedBigInteger('lp_clicks')->nullable()->default(null);
            $table->decimal('lp_ctr')->nullable()->default(null);
            $table->bigInteger('binom_leads')->nullable()->default(null);
            $table->decimal('binom_cpl')->nullable()->default(null);
            $table->bigInteger('affiliate_valid_leads')->nullable()->default(null);
            $table->bigInteger('affiliate_leads')->nullable()->default(null);
            $table->bigInteger('leads')->nullable()->default(null);
            $table->bigInteger('valid_leads')->nullable()->default(null);
            $table->decimal('offer_cr')->nullable()->default(null);
            $table->decimal('cost')->nullable()->default(null);

            $table->timestamps();

            $table->index(['datetime', 'offer_id']);

            $table->foreign('offer_id')
                ->references('id')
                ->on('offers');
        });
    }
}
