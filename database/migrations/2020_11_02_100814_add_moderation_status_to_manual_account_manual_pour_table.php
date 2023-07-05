<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModerationStatusToManualAccountManualPourTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manual_account_manual_pour', function (Blueprint $table) {
            $table->string('moderation_status')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('moderation_status', function (Blueprint $table) {
            $table->dropColumn('moderation_status');
        });
    }
}
