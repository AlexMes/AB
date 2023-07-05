<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFbDefaultTokenToGoogleApps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('google_apps', function (Blueprint $table) {
            $table->string('fb_token')->nullable()->default(null);
        });
    }
}
