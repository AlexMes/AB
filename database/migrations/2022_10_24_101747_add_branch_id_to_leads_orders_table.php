<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBranchIdToLeadsOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads_orders', function (Blueprint $table) {
            $table->foreignId('branch_id')
                ->index()
                ->nullable()
                ->default(null)
                ->constrained('branches')
                ->onUpdate('cascade')
                ->onDelete('set null');
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
            $table->dropColumn('branch_id');
        });
    }
}
