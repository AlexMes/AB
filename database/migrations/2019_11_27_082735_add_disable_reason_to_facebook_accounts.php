<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDisableReasonToFacebookAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('facebook_ads_accounts', function (Blueprint $table) {
            $table->string('disable_reason')->nullable()->default(null);
        });
    }
}
