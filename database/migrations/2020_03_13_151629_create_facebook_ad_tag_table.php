<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacebookAdTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_ad_tag', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ad_id');
            $table->unsignedBigInteger('tag_id');
            $table->timestamps();

            $table->foreign('tag_id')
                ->on('tags')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('ad_id')
                ->on('facebook_ads')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
