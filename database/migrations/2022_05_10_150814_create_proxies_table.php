<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProxiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proxies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('login');
            $table->string('password');
            $table->string('protocol');
            $table->string('host');
            $table->string('port');
            $table->string('geo')->nullable()->default(null);
            $table->foreignId('branch_id')
                ->index()
                ->nullable()
                ->default(null)
                ->constrained('branches')
                ->onUpdate('cascade')
                ->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proxies');
    }
}
