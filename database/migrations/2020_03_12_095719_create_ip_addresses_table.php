<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIpAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ip_addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ip')->unique();
            $table->string("city")->nullable()->default(null);
            $table->string("region")->nullable()->default(null);
            $table->string("region_code")->nullable()->default(null);
            $table->string("country")->nullable()->default(null);
            $table->string("country_code")->nullable()->default(null);
            $table->string("country_code_iso3")->nullable()->default(null);
            $table->string("country_name")->nullable()->default(null);
            $table->string("country_capital")->nullable()->default(null);
            $table->string("country_tld")->nullable()->default(null);
            $table->string("continent_code")->nullable()->default(null);
            $table->boolean("in_eu")->default(false);
            $table->string("postal")->nullable()->default(null);
            $table->string("latitude")->nullable()->default(null);
            $table->string("longitude")->nullable()->default(null);
            $table->string("timezone")->nullable()->default(null);
            $table->string("utc_offset")->nullable()->default(null);
            $table->string("country_calling_code")->nullable()->default(null);
            $table->string("country_area")->nullable()->default(null);
            $table->string("country_population")->nullable()->default(null);
            $table->string("currency")->nullable()->default(null);
            $table->string("currency_name")->nullable()->default(null);
            $table->string("languages")->nullable()->default(null);
            $table->string("asn")->nullable()->default(null);
            $table->string("org")->nullable()->default(null);

            $table->timestamps();
        });
    }
}
