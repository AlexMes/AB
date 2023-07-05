<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFrequencyToPlatformInsights extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('facebook_platform_insights', function (Blueprint $table) {
            $table->decimal('frequency', 10, 6)->default(0.00);
        });
    }
}
