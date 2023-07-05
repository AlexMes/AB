<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFkFromOrdersToRegistrars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('registrar_id')->references('id')
                ->on('registrars')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
