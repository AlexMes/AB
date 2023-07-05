<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacebookCommentsTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_comments_templates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('message');
            $table->timestamps();
        });
    }
}
