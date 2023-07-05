<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_apps', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('market_id')->unique();
            $table->boolean('enabled')->default(false);
            $table->string('url');
            $table->timestamps();
        });
    }
}
