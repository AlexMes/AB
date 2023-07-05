<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacebookAdsAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_ads_accounts', function (Blueprint $table) {
            $table->string('id')->index();
            $table->string('account_id');
            $table->string('name');
            $table->string('age');
            $table->string('account_status');
            $table->string('amount_spent');
            $table->string('balance');
            $table->string('currency');
            $table->unsignedBigInteger('profile_id')->index();
            $table->timestamps();

            $table->foreign('profile_id')
                ->references('id')
                ->on('facebook_accounts')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
