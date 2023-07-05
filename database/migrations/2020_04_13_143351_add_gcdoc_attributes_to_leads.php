<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGcdocAttributesToLeads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedBigInteger('gdoc_manager')->nullable()->default(null);
            $table->dateTime('gdoc_called_at')->nullable()->default(null);
            $table->string('gdoc_status')->nullable()->default(null);
            $table->string('gdoc_note')->nullable()->default(null);
            $table->string('gdoc_gender')->nullable()->default(null);
            $table->string('gdoc_profession')->nullable()->default(null);
            $table->string('gdoc_age')->nullable()->default(null);
            $table->string('gdoc_timezone')->nullable()->default(null);
            $table->string('gdoc_deposit_sum')->nullable()->default(null);
            $table->text('gdoc_comment')->nullable()->default(null);
        });
    }
}
