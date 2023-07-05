<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToBinomStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('binom_statistics', function (Blueprint $table) {
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
        });
    }
}
