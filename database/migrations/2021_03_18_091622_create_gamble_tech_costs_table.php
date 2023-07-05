<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGambleTechCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gamble_tech_costs', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('user_id')
                ->index()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->decimal('spend')->default(0);
            $table->timestamps();

            $table->unique(['date', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gamble_tech_costs');
    }
}
