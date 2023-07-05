<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManualBundlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_bundles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('offer_id')->index();
            $table->timestamps();

            $table->foreign('offer_id')
                ->on('offers')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
