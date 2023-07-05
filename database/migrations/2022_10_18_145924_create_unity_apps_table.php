<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnityAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unity_apps', function (Blueprint $table) {
            $table->string('id')->unique();
            $table->string('name');
            $table->string('store');
            $table->string('store_id')->nullable()->default(null);
            $table->string('adomain')->nullable()->default(null);
            $table->foreignId('organization_id')
                ->index()
                ->constrained('unity_organizations')
                ->onUpdate('cascade')
                ->onDelete('cascade');
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
        Schema::dropIfExists('unity_apps');
    }
}
