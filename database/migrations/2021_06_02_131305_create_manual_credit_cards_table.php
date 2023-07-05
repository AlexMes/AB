<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManualCreditCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_credit_cards', function (Blueprint $table) {
            $table->id();
            $table->string('digits');
            $table->string('number');
            $table->string('bank_name');
            $table->string('social_profile')->nullable()->default(null);
            $table->foreignId('buyer_id')
                ->index()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('account_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manual_credit_cards');
    }
}
