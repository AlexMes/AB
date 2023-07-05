<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGambleInsightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gamble_insights', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('account_id')->index();
            $table->unsignedBigInteger('campaign_id')->index();
            $table->integer('impressions')->default(0);
            $table->integer('installs')->default(0);
            $table->unsignedDecimal('spend')->default(0);
            $table->integer('registrations')->default(0);
            $table->timestamps();

            $table->unique(['date', 'account_id', 'campaign_id']);

            $table->foreign('account_id')
                ->references('id')
                ->on('gamble_accounts')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('campaign_id')
                ->references('id')
                ->on('gamble_campaigns')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
