<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManualAccountModerationLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_account_moderation_logs', function (Blueprint $table) {
            $table->id();
            $table->string('account_id')->index();
            $table->string('original')->nullable()->default(null);
            $table->string('changed');
            $table->timestamps();

            $table->foreign('account_id')
                ->on('manual_accounts')
                ->references('account_id')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
