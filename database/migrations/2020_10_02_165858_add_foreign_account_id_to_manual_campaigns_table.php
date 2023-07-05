<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignAccountIdToManualCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manual_campaigns', function (Blueprint $table) {
            $table->foreign('account_id')
                ->on('manual_accounts')
                ->references('account_id')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
