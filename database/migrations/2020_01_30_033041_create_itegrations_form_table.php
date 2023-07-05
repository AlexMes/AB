<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItegrationsFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('itegrations_forms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('url');
            $table->string('method')->nullable()->default('post');
            $table->string('form_id')->nullable()->default(null);
            $table->string('form_api_key')->nullable()->default(null);
            $table->json('fields')->nullable()->default(null);
            $table->boolean('status')->default(false)->index();
            $table->timestamps();
        });
    }
}
