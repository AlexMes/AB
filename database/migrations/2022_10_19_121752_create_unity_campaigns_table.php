<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnityCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unity_campaigns', function (Blueprint $table) {
            $table->string('id')->unique();
            $table->string('name');
            $table->string('goal');
            $table->boolean('enabled');
            $table->string('app_id')->index();
            $table->timestamps();

            $table->foreign('app_id')->references('id')->on('unity_apps')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unity_campaigns');
    }
}
