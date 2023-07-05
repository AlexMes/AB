<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadDestinationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_destinations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('driver')->nullable()->default(null);
            $table->jsonb('configuration')->nullable()->default(null);
            $table->timestamps();
        });
    }
}
