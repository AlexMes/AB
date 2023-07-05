<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLinkClicksToInsightsCacheTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('facebook_cached_insights', function (Blueprint $table) {
            $table->string('link_clicks')->nullable()->default(null);
        });
    }
}
