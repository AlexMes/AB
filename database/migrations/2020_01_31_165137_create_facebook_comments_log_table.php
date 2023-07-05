<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacebookCommentsLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_comments_log', function (Blueprint $table) {
            $table->string('id')->index();
            $table->string('account_id')->nullable()->default(null);
            $table->bigInteger('page_id')->unsigned()->nullable();
            $table->string('published_at');
            $table->text('text');
            $table->dateTime('deleted_at', 0);
            $table->timestamps();
            $table->foreign('page_id')
                ->references('id')
                ->on('facebook_profile_pages')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }
}
