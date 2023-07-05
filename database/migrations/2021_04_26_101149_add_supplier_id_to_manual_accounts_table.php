<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSupplierIdToManualAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manual_accounts', function (Blueprint $table) {
            $table->foreignId('supplier_id')
                ->index()
                ->nullable()
                ->default(null)
                ->constrained('manual_suppliers')
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
        Schema::table('manual_accounts', function (Blueprint $table) {
            $table->dropColumn('supplier_id');
        });
    }
}
