<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfileLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_profile_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profile_id');
            $table->unsignedInteger('duration')->default(0);
            $table->string('miniature')->nullable()->default(null);
            $table->string('creative')->nullable()->default(null);
            $table->text('text')->nullable()->default(null);
            $table->string('link')->nullable()->default(null);
            $table->timestamps();

            $table->foreign('profile_id')
                ->on('facebook_profiles')
                ->references('id')
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
        Schema::dropIfExists('facebook_profile_logs');
    }
}
