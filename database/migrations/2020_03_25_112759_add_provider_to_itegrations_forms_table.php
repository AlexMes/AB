<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProviderToItegrationsFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('itegrations_forms', function (Blueprint $table) {
            $table->string('provider')->nullable()->default('default')->index();
            $table->string('phone_prefix')->nullable()->default(null)->index();
            $table->string('external_offer_id')->nullable()->default(null)->index();
            $table->string('external_affiliate_id')->nullable()->default(null)->index();
        });
    }
}
