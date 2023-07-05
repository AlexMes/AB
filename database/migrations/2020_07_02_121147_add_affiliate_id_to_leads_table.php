<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAffiliateIdToLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedBigInteger('affiliate_id')
                ->nullable()
                ->default(null)
                ->index();

            $table->foreign('affiliate_id')
                ->references('id')
                ->on('affiliates')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }
}
