<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOfficeIdToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('office_id')->nullable()->default(null);

            $table->foreign('office_id')
                ->references('id')
                ->on('offices')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
