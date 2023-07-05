<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBranchIdToManualSuppliers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manual_suppliers', function (Blueprint $table) {
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
        Schema::table('manual_suppliers', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
    }
}
