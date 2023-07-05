<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubstituteOfferToResellBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resell_batches', function (Blueprint $table) {
            $table->unsignedBigInteger('substitute_offer')->nullable();
        });
    }
}
