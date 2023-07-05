<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacebookSentCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_sent_comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('profile_id')->index();
            $table->string('ad_id')->index();
            $table->string('comment_id')->unique();
            $table->string('message');
            $table->timestamp('sent_at')->nullable()->default(null);
            $table->timestamps();


            $table->foreign('profile_id')
                ->references('id')
                ->on('facebook_profiles')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('ad_id')
                ->references('id')
                ->on('facebook_ads')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
