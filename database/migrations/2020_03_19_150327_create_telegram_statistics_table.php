<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelegramStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('binom_telegram_statistics', function (Blueprint $table) {
            $table->date('date');
            $table->unsignedBigInteger('campaign_id')->index();
            $table->unsignedBigInteger('clicks');
            $table->unsignedBigInteger('lp_clicks');
            $table->unsignedBigInteger('lp_views');
            $table->unsignedBigInteger('unique_clicks');
            $table->unsignedBigInteger('unique_camp_clicks');
            $table->unsignedBigInteger('leads');
            $table->string('utm_source')
                ->nullable()
                ->default(null)
                ->index();
            $table->string('utm_campaign')
                ->nullable()
                ->default(null)
                ->index();
            $table->string('fb_campaign_id')
                ->nullable()
                ->default(null)
                ->index();
            $table->string('account_id')
                ->nullable()
                ->default(null)
                ->index();
            $table->unsignedBigInteger('user_id')
                ->nullable()
                ->default(null)
                ->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('binom_telegram_statistics');
    }
}
