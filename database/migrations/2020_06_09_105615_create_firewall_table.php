<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFirewallTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('firewall_rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index();
            $table->ipAddress('ip');
            $table->timestamps();

            $table->unique(['user_id', 'ip']);

            $table->foreign('user_id')
                ->on('users')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
