<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBranchToBatches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resell_batches', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable()->default(null);
        });
    }
}
