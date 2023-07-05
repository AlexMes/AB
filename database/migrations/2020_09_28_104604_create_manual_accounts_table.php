<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManualAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_id');
            $table->string('name');
            $table->unsignedBigInteger('user_id')->nullable()->default(null);
            $table->timestamps();

            $table->foreign('user_id')
                ->on('users')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }
}
