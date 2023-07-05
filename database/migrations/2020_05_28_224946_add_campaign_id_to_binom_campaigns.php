<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampaignIdToBinomCampaigns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('binom_campaigns', function (Blueprint $table) {
            $table->unsignedBigInteger('campaign_id')->nullable()->default(null)->index();
        });
        DB::table('binom_campaigns')->update([
            'campaign_id' => DB::raw('"id"'),
        ]);
    }
}
