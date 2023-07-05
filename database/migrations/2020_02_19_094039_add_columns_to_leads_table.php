<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedInteger('status')->nullable()->default(null);
            $table->string('responsible')->nullable()->default(null);
            $table->unsignedInteger('office_id')->nullable()->default(null);
            $table->unsignedBigInteger('department_id')->nullable()->default(null);
            $table->datetime('called_at')->nullable()->default(null);
        });
    }
}
