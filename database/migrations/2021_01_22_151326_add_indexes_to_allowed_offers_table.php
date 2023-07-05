<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToAllowedOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('allowed_offers', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('offer_id');
            $table->unique(['user_id', 'offer_id']);
        });
    }
}
