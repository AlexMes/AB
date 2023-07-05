<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSpIdToDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->unsignedBigInteger('sp_id')->nullable()->default(null);


            $table->foreign('sp_id')->references('id')
                ->on('pages')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
