<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficeResellBatchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('office_resell_batch', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('office_id')->index();
            $table->unsignedBigInteger('batch_id')->index();

            $table->unique(['office_id', 'batch_id']);

            $table->foreign('office_id')
                ->on('offices')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('batch_id')
                ->on('resell_batches')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('office_resell_batch');
    }
}
