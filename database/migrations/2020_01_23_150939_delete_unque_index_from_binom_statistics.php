<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteUnqueIndexFromBinomStatistics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('binom_statistics', function (Blueprint $table) {
            $table->dropUnique('binom_statistics_date_campaign_id_unique');
        });
    }
}
