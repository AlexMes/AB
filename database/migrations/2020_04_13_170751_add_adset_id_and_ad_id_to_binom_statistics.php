<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdsetIdAndAdIdToBinomStatistics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('binom_statistics', function (Blueprint $table) {
            $table->string('fb_adset_id')->nullable()->default(null)->index();
            $table->string('fb_ad_id')->nullable()->default(null)->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('binom_statistics', function (Blueprint $table) {
            $table->dropColumn('fb_ad_id');
            $table->dropColumn('fb_adset_id');
        });
    }
}
