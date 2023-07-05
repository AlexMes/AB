<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBpIdToDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->unsignedBigInteger('bp_id')->nullable()->default(null);


            $table->foreign('bp_id')->references('id')
                ->on('pages')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
