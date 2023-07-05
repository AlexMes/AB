<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGroupIdToFacebookAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('facebook_ads_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('group_id')->nullable()->default(null);

            $table->foreign('group_id')
                ->references('id')
                ->on('facebook_account_groups')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
