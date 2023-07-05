<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeUniqueIndexInLeadsOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads_orders', function (Blueprint $table) {
            $table->dropUnique(['date', 'office_id']);
            $table->unique(['date', 'office_id', 'branch_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads_orders', function (Blueprint $table) {
            $table->dropUnique(['date', 'office_id', 'branch_id']);
            $table->unique(['date', 'office_id']);
        });
    }
}
