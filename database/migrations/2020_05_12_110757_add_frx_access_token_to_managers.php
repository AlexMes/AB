<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFrxAccessTokenToManagers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('managers', function (Blueprint $table) {
            $table->text('frx_access_token')->nullable()->default(null);
        });
    }
}
