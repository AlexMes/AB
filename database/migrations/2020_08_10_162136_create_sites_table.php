<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('forge_id');
            $table->unsignedBigInteger('server_id');
            $table->string('name');
            $table->string('status')->nullable()->default(null);
            $table->string('repository')->nullable()->default(null);
            $table->string('directory')->nullable()->default(null);
            $table->string('app')->nullable()->default(null);
            $table->string('app_status')->nullable()->default(null);
            $table->boolean('has_certificates')->nullable()->default(null);
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
        Schema::dropIfExists('sites');
    }
}
