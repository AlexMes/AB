<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleSheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_sheets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('spreadsheet_id')->index();
            $table->unsignedBigInteger('sheet_id');
            $table->unsignedBigInteger('index');
            $table->timestamps();
        });
    }
}
