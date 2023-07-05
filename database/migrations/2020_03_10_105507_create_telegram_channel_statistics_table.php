<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelegramChannelStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telegram_channel_statistics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->unsignedBigInteger('channel_id')->index();
            $table->decimal('cost');
            $table->unsignedBigInteger('impressions')->default(0);
            $table->timestamps();


            $table->unique(['date', 'channel_id']);
            $table->foreign('channel_id')
                ->references('id')
                ->on('telegram_channels')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
