<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUtmKeyToManualCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manual_campaigns', function (Blueprint $table) {
            $table->string('utm_key')->nullable()->default(null)->index();
        });
    }
}
