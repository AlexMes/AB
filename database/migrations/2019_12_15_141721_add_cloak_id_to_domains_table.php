<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCloakIdToDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->unsignedBigInteger('cloak_id')->nullable()->default(null);

            $table->foreign('cloak_id')->references('id')
                ->on('cloaks')
                ->onUpdate('set null')
                ->onDelete('set null');
        });
    }
}
