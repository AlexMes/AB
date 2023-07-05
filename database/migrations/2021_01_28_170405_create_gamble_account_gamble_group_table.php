<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGambleAccountGambleGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gamble_account_gamble_group', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')
                ->index()
                ->constrained('gamble_accounts')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('group_id')
                ->index()
                ->constrained('gamble_groups')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->unique(['account_id', 'group_id']);
        });
    }
}
