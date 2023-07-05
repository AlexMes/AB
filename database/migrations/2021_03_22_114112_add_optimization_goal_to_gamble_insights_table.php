<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOptimizationGoalToGambleInsightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gamble_insights', function (Blueprint $table) {
            $table->string('optimization_goal')->nullable()->default(null);
        });
    }
}
