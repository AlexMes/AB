<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProgressToLeadsOrderRoutes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_order_routes', function (Blueprint $table) {
            $table->unsignedDecimal('progress', 6, 2)
                ->storedAs('COALESCE(round("leadsReceived" / nullif("leadsOrdered", 0) * 100, 2), 0.0)');
        });
    }
}
