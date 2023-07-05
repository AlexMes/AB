<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountOfferGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_offer_groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->string('account_id')->index();
            $table->unsignedBigInteger('offer_id')->index();
            $table->timestamps();

            $table->foreign('account_id')
                ->references('id')
                ->on('facebook_ads_accounts')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('offer_id')
                ->references('id')
                ->on('offers')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
