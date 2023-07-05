<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropUniqueIndexFromFacebookAdsAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('facebook_ads_accounts', function (Blueprint $table) {
            $table->dropUnique('facebook_ads_accounts_id_unique');
        });
    }
}
