<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManualCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_campaigns', function (Blueprint $table) {
            $table->string('id')->unique();
            $table->string('name');
            $table->string('account_id')->index();
            $table->unsignedBigInteger('bundle_id')->index();
            $table->timestamps();

            $table->foreign('bundle_id')
                ->on('manual_bundles')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
