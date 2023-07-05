<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropLabelLeadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('label_lead');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('label_lead', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('label_id');
            $table->unsignedBigInteger('lead_id');
            $table->timestamps();

            $table->unique(['label_id', 'lead_id']);

            $table->foreign('label_id')
                ->references('id')
                ->on('labels')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('lead_id')
                ->references('id')
                ->on('leads')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
