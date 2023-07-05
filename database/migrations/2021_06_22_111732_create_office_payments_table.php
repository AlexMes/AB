<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('office_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')
                ->index()
                ->constrained('offices')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->unsignedSmallInteger('paid')->default(0);
            $table->unsignedSmallInteger('assigned')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('office_payments');
    }
}
