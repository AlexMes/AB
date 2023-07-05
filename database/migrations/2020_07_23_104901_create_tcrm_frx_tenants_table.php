<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTcrmFrxTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tcrm_frx_tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key')->unique();
            $table->string('url');
            $table->unsignedInteger('client_id');
            $table->string('client_secret');
            $table->timestamps();
        });
    }
}
