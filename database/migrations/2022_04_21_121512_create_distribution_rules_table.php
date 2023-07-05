<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistributionRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distribution_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')
                ->nullable()
                ->index()
                ->constrained('offices')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('offer_id')
                ->nullable()
                ->index()
                ->constrained('offers')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('geo');
            $table->string('country_name');
            $table->boolean('allowed')->default(false);

            $table->unique(['office_id', 'offer_id', 'geo']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distribution_rules');
    }
}
