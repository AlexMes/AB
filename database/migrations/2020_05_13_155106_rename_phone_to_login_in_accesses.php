<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePhoneToLoginInAccesses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accesses', function (Blueprint $table) {
            if (! app()->environment('testing')) {
                $table->renameColumn('phone', 'login');
            }
        });
    }
}
