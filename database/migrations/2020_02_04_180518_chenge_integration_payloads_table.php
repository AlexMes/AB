<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChengeIntegrationPayloadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('integration_payloads', function (Blueprint $table) {
            $table->unsignedBigInteger('offer_id')->nullable()->default(null)->change();
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
