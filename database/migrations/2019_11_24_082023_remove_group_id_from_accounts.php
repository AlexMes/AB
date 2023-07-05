<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveGroupIdFromAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('facebook_accounts', function (Blueprint $table) {
            $table->dropForeign('facebook_accounts_group_id_foreign');
            $table->dropColumn('group_id');
        });
    }
}
