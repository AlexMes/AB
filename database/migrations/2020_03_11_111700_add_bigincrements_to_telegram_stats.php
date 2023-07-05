<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBigincrementsToTelegramStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('telegram_channel_statistics', function (Blueprint $table) {
            if (! app()->environment('testing')) {
                $table->bigIncrements('id');
            }
        });
    }
}
