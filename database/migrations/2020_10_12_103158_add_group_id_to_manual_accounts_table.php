<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGroupIdToManualAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manual_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('group_id')->nullable()->default(null);

            $table->foreign('group_id')
                ->on('manual_groups')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }
}
