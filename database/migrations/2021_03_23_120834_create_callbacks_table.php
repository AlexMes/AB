<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCallbacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('callbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->index()
                ->constrained('lead_order_assignments')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('phone');
            $table->timestamp('call_at')->nullable()->default(null);
            $table->timestamp('called_at')->nullable()->default(null);
            $table->string('frx_call_id')->nullable()->default(null);
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
        Schema::dropIfExists('callbacks');
    }
}
