<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('role')->default('member');
            $table->timestamps();


            $table->unique(['team_id','user_id']);

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onUpdate('cascade')->onDelete('cascade');
        });
    }
}
