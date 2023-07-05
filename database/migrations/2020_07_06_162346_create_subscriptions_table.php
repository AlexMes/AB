<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('service');
            $table->string('account');
            $table->boolean('enabled');
            $table->string('amount')->nullable()->default(null);
            $table->string('frequency');
            $table->timestamps();
        });
    }
}
