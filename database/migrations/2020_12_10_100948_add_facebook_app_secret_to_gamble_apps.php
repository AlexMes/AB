<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFacebookAppSecretToGambleApps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('google_apps', function (Blueprint $table) {
            $table->string('fb_app_secret')->nullable()->default(null);
        });
    }
}
