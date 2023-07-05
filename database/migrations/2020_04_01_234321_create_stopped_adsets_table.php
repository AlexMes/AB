<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoppedAdsetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stopped_adsets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('account_id')->index();
            $table->string('adset_id')->index();
            $table->decimal('spend')->default(0);
            $table->decimal('cpl')->default(0);
            $table->timestamps();
        });
    }
}
