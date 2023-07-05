<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadMarkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_markers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('lead_id')->index();
            $table->timestamps();

            $table->unique(['name','lead_id']);

            $table->foreign('lead_id')->references('id')->on('leads')->onUpdate('cascade')->onDelete('cascade');
        });
    }
}
