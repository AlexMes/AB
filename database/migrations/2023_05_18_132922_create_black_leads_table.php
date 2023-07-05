<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlackLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('black_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')
                ->nullable()
                ->default(null)
                ->index()
                ->constrained('branches')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('phone')->nullable()->default(null);
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
        Schema::dropIfExists('black_leads');
    }
}
