<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddManagerIdToLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedBigInteger('manager_id')->nullable()->default(null);

            $table->foreign('manager_id')
                ->on('managers')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }
}
