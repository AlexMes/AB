<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRedirectUrlToAssignments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_order_assignments', function (Blueprint $table) {
            $table->string('redirect_url')->nullable()->default(null);
        });
    }
}
