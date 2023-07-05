<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadPaymentConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_payment_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')
                ->index()
                ->constrained('offices')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('offer_id')
                ->index()
                ->constrained('offers')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('model');
            $table->string('cost');
            $table->timestamps();

            $table->unique(['office_id', 'offer_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lead_payment_conditions');
    }
}
