<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBuyerAndUtmToGoogleAppHitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * Here we recreate fucking hits table, to make shits right
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_app_hits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained('google_apps');
            $table->foreignId('link_id')->nullable()->constrained('google_app_links');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->boolean('is_passed')->default(true);
            $table->string('geo')->nullable();
            $table->ipAddress('ip');
            $table->string('url');
            $table->string('utm_source')->nullable()->default(null);
            $table->string('utm_campaign')->nullable()->default(null);
            $table->string('utm_content')->nullable()->default(null);
            $table->string('utm_term')->nullable()->default(null);
            $table->string('utm_medium')->nullable()->default(null);
            $table->string('reason')->nullable()->default(null);
            $table->timestamps();
        });
    }
}
