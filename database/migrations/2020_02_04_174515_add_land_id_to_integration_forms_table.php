<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLandIdToIntegrationFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('itegrations_forms', function (Blueprint $table) {
            $table->unsignedBigInteger('landing_id')
                ->nullable()
                ->default(null)
                ->index();

            $table->foreign('landing_id')
                ->references('id')
                ->on('domains')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
