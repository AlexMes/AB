<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGroupIdToFacebookProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('facebook_profiles', function (Blueprint $table) {
            $table->unsignedBigInteger('group_id')->nullable()->default(null);

            $table->foreign('group_id')
                ->on('groups')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }
}
