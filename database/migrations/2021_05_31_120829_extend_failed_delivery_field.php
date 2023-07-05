<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ExtendFailedDeliveryField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_order_assignments', function (Blueprint $table) {
            $table->text('delivery_failed')->nullable()->change();
        });
    }
}
