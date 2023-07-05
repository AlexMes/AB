<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIntegrationFormIdToOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->unsignedBigInteger('integration_form_id')->nullable()->default(null)->index();

            $table->foreign('integration_form_id')
                ->references('id')
                ->on('itegrations_forms')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }
}
