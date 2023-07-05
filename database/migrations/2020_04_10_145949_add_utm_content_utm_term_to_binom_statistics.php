<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUtmContentUtmTermToBinomStatistics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('binom_statistics', function (Blueprint $table) {
            $table->string('utm_term')->nullable()->default(null)->index();
            $table->string('utm_content')->nullable()->default(null)->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('binom_statistics', function (Blueprint $table) {
            $table->dropColumn('utm_content');
            $table->dropColumn('utm_term');
        });
    }
}
