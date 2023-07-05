<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhoneLookupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phone_lookups', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->unique();
            $table->string('country')->index();
            $table->string('prefix')->index();
            $table->string('type')->default('unknown');
            $table->timestamps();
        });
    }
}
