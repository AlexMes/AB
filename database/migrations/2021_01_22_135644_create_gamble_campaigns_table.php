<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGambleCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gamble_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('campaign_id')->unique();
            $table->string('name');
            $table->unsignedBigInteger('account_id')->index();
            $table->timestamps();

            $table->foreign('account_id')
                ->on('gamble_accounts')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
