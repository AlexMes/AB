<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppHitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_app_hits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('google_app_id')->constrained();
            $table->boolean('is_passed')->default(true);
            $table->string('geo')->nullable();
            $table->ipAddress('ip');
            $table->string('url');
            $table->timestamps();
        });
    }
}
