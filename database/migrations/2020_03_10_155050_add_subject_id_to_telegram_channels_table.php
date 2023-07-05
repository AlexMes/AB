<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubjectIdToTelegramChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('telegram_channels', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_id')->nullable()->default(null);

            $table->foreign('subject_id')
                ->references('id')
                ->on('telegram_channel_subjects')
                ->onUpdate('set null')
                ->onDelete('set null');
        });
    }
}
