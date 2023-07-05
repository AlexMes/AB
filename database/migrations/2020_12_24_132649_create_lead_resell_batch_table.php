<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadResellBatchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_resell_batch', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id')->index();
            $table->unsignedBigInteger('batch_id')->index();
            $table->timestamp('assigned_at')->nullable()->default(null);

            $table->unique(['lead_id', 'batch_id']);

            $table->foreign('lead_id')
                ->on('leads')
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
        Schema::dropIfExists('lead_resell_batch');
    }
}
