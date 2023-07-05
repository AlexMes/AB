<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCollectErrorsToLeadDestinationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_destinations', function (Blueprint $table) {
            $table->text('collect_results_error')->nullable()->default(null);
            $table->text('collect_statuses_error')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lead_destinations', function (Blueprint $table) {
            $table->dropColumn('collect_results_error');
            $table->dropColumn('collect_statuses_error');
        });
    }
}
