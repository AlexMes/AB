<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToFacebookAdsAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('facebook_ads_accounts', function (Blueprint $table) {
            $table->foreign('group_id')
                ->on('groups')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }
}
