<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllowedBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allowed_branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->index()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('branch_id')
                ->index()
                ->constrained('branches')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->unique(['user_id', 'branch_id']);
        });
    }
}
