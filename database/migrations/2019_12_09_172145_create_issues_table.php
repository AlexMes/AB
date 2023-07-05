<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('issues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('issuable_id')->index();
            $table->string('issuable_type')->index();
            $table->text('message');
            $table->timestamp('fixed_at')->nullable()->default(null);
            $table->timestamps();
        });
    }
}
