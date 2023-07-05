<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAffiliatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('offer_id')->nullable()->default(null);
            $table->string('api_key', 250)->unique();
            $table->decimal('cpl')->default(0.00);
            $table->decimal('cpa')->default(0.00);

            $table->timestamps();
        });
    }
}
