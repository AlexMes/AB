<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixFanstasticallyStupidNullIssueOnAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('facebook_ads_accounts', function (Blueprint $table) {
            if (! app()->environment('testing')) {
                $table->string('comment')->nullable()->default(null)->change();
            };
        });
    }
}
