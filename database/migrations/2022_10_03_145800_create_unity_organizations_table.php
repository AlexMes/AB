<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnityOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unity_organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('organization_core_id');
            $table->string('organization_id');
            $table->string('api_key');
            $table->string('key_id');
            $table->string('secret_key');
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
        Schema::dropIfExists('unity_organizations');
    }
}
