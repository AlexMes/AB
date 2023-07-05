<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVkFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vk_forms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('vk_id')->index();
            $table->string('vk_group_id')->index();
            $table->json('questions')->nullable()->default(null);
            $table->json('raw_data')->nullable()->default(null);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('vk_group_id')
                ->references('vk_id')
                ->on('vk_groups')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->unique(['vk_id', 'vk_group_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vk_forms');
    }
}
