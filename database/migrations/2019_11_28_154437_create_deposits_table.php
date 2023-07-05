<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->datetime('lead_return_date')->nullable()->default(null);
            $table->datetime('date')->nullable()->default(null);
            $table->integer('sum')->nullable()->default(null);
            $table->string('phone')->nullable()->default(null);

            $table->string('account_id')->nullable()->default(null)->index();
            $table->unsignedBigInteger('user_id')->nullable()->default(null)->index();
            $table->unsignedBigInteger('office_id')->nullable()->default(null)->index();
            $table->unsignedBigInteger('offer_id')->nullable()->default(null)->index();
            $table->unsignedBigInteger('lead_id')->nullable()->default(null)->index();

            $table->foreign('account_id')
                ->references('id')
                ->on('facebook_ads_accounts')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('office_id')
                ->references('id')
                ->on('offices')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('offer_id')
                ->references('id')
                ->on('offers')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('lead_id')
                ->references('id')
                ->on('leads')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }
}
