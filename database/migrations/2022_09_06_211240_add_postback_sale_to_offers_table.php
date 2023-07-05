<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPostbackSaleToOffersTable extends Migration
{
    public function up()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->string('pb_sale')->nullable()->default(null);
        });
    }
}
