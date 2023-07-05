<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBundlePlacementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bundle_placement', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('bundle_id')->index();
            $table->unsignedBigInteger('placement_id')->index();
            $table->timestamps();

            $table->unique(['bundle_id', 'placement_id']);

            $table->foreign('bundle_id')
                ->on('bundles')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('placement_id')
                ->on('placements')
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
        Schema::dropIfExists('bundle_placement');
    }
}
