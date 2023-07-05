<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeadIdIndexToLeadOrderAssignments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_order_assignments', function (Blueprint $table) {
            $table->index('lead_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lead_order_assignments', function (Blueprint $table) {
            $table->dropIndex('lead_order_assignments_lead_id_index');
        });
    }
}
