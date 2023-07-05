<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVkProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vk_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('vk_id')->unique();
            $table->text('token');
            $table->foreignId('user_id')
                ->index()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->json('issues_info')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vk_profiles');
    }
}
