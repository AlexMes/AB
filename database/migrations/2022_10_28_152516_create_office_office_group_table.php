<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficeOfficeGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('office_office_group', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')
                ->index()
                ->constrained('office_groups')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('office_id')
                ->index()
                ->constrained('offices')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->unique(['group_id', 'office_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('office_office_group');
    }
}
