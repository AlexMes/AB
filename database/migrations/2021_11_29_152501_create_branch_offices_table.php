<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_offices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('office_id')->index();
            $table->unsignedBigInteger('branch_id')->index();
            $table->timestamps();

            $table->unique(['office_id','branch_id']);

            $table->foreign('office_id')->references('id')->on('offices')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onUpdate('cascade')->onDelete('cascade');
        });
    }
}
