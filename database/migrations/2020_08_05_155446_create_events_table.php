<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('eventable_id');
            $table->string('eventable_type');
            $table->string('type');
            $table->json('original_data')->nullable()->default(null);
            $table->json('changed_data')->nullable()->default(null);
            $table->json('custom_data')->nullable()->default(null);
            $table->unsignedBigInteger('auth_id')->nullable()->default(null);
            $table->string('auth_type')->nullable()->default(null);
            $table->timestamps();

            $table->index(['eventable_id', 'eventable_type']);
        });
    }
}
