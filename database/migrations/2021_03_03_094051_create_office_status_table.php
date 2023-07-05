<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficeStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('office_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')
                ->index()
                ->constrained('offices')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('status');
            $table->boolean('selectable')->default(true);

            $table->unique(['office_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('office_status');
    }
}
