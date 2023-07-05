<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdStatusLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs_facebook_ads_disapprovals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ad_id')->index();
            $table->string('reason', 1024)->nullable()->default(null);
            $table->timestamps();
        });
    }
}
