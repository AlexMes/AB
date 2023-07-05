<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropGroupIdFromManualAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manual_accounts', function (Blueprint $table) {
            if (!app()->environment('testing')) {
                $table->dropColumn('group_id');
            }
        });
    }
}
