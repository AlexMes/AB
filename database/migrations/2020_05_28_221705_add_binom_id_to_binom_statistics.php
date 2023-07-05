<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBinomIdToBinomStatistics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('binom_statistics', function (Blueprint $table) {
            $table->unsignedBigInteger('binom_id')->nullable()->default(null)->index();
        });
    }
}
