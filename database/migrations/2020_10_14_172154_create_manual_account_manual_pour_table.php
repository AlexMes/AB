<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManualAccountManualPourTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_account_manual_pour', function (Blueprint $table) {
            $table->id();
            $table->string('account_id')->index();
            $table->unsignedBigInteger('pour_id')->index();
            $table->timestamps();

            $table->unique(['account_id', 'pour_id']);

            $table->foreign('account_id')
                ->on('manual_accounts')
                ->references('account_id')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('pour_id')
                ->on('manual_pours')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
