<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFrxUserRoleToManagers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('managers', function (Blueprint $table) {
            $table->string('frx_role')->nullable()->default(null);
        });
    }
}
