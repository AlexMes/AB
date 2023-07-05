<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOfferIdSafePageHostIpColumnsToDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->unsignedBigInteger('offer_id')->nullable()->default(null)->index();
            $table->string('safe_page')->nullable()->default(null);
            $table->string('host')->nullable()->default(null);
            $table->string('ip')->nullable()->default(null);

            $table->foreign('offer_id')
                ->references('id')
                ->on('offers')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
