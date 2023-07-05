<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFildsIntegrationPayloads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('integration_payloads', function (Blueprint $table) {
            $table->text('requestUrl')->nullable()->default(null)->change();
        });
    }
}
