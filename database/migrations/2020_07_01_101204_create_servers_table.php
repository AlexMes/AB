<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('forge_id');
            $table->string('name');
            $table->string('provider');
            $table->ipAddress('ip_address');
            $table->string('region')->nullable()->default(null);
            $table->boolean('is_ready')->default(false);
            $table->timestamps();
        });
    }
}
