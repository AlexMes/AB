<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDomainIdToFacebookAds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('facebook_ads', function (Blueprint $table) {
            $table->unsignedBigInteger('domain_id')->nullable()->default(null);
        });
    }
}
