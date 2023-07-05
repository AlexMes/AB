<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSharedUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shared_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->index()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('shared_id')
                ->index()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->unique(['user_id', 'shared_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shared_users');
    }
}
