<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveForeignKeyFromInsightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('facebook_cached_insights', function (Blueprint $table) {
            $table->dropForeign('facebook_cached_insights_account_id_foreign');
        });
    }
}
