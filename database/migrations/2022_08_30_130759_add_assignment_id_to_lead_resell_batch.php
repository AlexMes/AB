<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAssignmentIdToLeadResellBatch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_resell_batch', function (Blueprint $table) {
            $table->unsignedBigInteger('assignment_id')->index()->nullable()->default(null);

            $table->foreign('assignment_id')->references('id')->on('lead_order_assignments')->onUpdate('cascade')->onDelete('set null');
        });
    }
}
