<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_campaigns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->index();
            $table->string('text');
            $table->string('type')->nullable()->default(null)->index();
            $table->integer('after_minutes')->nullable()->default(null)->index();


            $table->unsignedBigInteger('landing_id')
                ->nullable()
                ->default(null)
                ->index();
            $table->boolean('status')
                ->default(true)
                ->index();
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('landing_id')
                ->references('id')
                ->on('domains')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
