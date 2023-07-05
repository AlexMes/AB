<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManualAccountManualGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_account_manual_group', function (Blueprint $table) {
            $table->id();
            $table->string('account_id');
            $table->unsignedBigInteger('group_id');

            $table->foreign('account_id')
                ->references('account_id')
                ->on('manual_accounts')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('group_id')
                ->references('id')
                ->on('manual_groups')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manual_account_manual_group');
    }
}
