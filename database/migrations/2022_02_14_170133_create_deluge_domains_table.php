<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDelugeDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deluge_domains', function (Blueprint $table) {
            $table->id();
            $table->string('url')->unique();
            $table->string('destination')->nullable()->default(null);
            $table->unsignedBigInteger('user_id')->index();
            $table->dateTime('last_checked_at')->nullable()->default(null);
            $table->dateTime('down_at')->nullable()->default(null);
            $table->timestamps();
        });
    }
}
