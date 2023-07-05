<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOfferIdToGambleCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gamble_campaigns', function (Blueprint $table) {
            $table->foreignId('offer_id')
                ->nullable()
                ->index()
                ->constrained('gamble_offers')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }
}
