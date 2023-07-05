<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTsIsToBinomTrafficSources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('binom_traffic_sources', function (Blueprint $table) {
            $table->unsignedBigInteger('ts_id')->nullable()->default(null);
        });
        DB::table('binom_traffic_sources')->update([
            'ts_id' => DB::raw('"id"'),
        ]);
    }
}
