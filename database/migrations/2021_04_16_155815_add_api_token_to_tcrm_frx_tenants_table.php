<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApiTokenToTcrmFrxTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tcrm_frx_tenants', function (Blueprint $table) {
            $table->string('api_token')->nullable();
            $table->unique('api_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tcrm_frx_tenants', function (Blueprint $table) {
            $table->dropColumn('api_token');
        });
    }
}
