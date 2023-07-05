<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToFacebookCachedInsights extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('facebook_cached_insights', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->default(null)->index();
        });
    }
}
