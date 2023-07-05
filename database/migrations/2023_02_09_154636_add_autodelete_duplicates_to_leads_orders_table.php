<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAutodeleteDuplicatesToLeadsOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads_orders', function (Blueprint $table) {
            $table->boolean('autodelete_duplicates')->default(false);
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
            $table->dropColumn('autodelete_duplicates');
        });
    }
}
