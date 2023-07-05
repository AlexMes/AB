<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleAppLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_app_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained('google_apps');
            $table->foreignId('user_id')->constrained('users');
            $table->boolean('enabled')->default(false);
            $table->string('geo')->nullable()->default(null);
            $table->string('url');
            $table->timestamps();
        });
    }
}
