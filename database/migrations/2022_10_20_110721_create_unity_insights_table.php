<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnityInsightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unity_insights', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->string('app_id')->index();
            $table->string('campaign_id')->index();
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('clicks')->default(0);
            $table->unsignedDecimal('spend')->default(0);
            $table->unsignedInteger('installs')->default(0);
            $table->timestamps();

            $table->unique(['date', 'app_id', 'campaign_id']);

            $table->foreign('app_id')->references('id')->on('unity_apps')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('campaign_id')->references('id')->on('unity_campaigns')
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
        Schema::dropIfExists('unity_insights');
    }
}
