<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGambleAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gamble_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_id')->unique();
            $table->string('name');
            $table->unsignedBigInteger('user_id')->nullable()->default(null)->index();
            $table->timestamps();

            $table->foreign('user_id')
                ->on('users')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }
}
