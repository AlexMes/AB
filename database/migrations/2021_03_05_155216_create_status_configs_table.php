<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')
                ->index()
                ->constrained('offices')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->unsignedSmallInteger('assigned_days_ago');
            $table->string('new_status');
            $table->json('statuses');
            $table->string('statuses_type');
            $table->boolean('enabled')->index()->default(false);
            $table->timestamps();
        });
    }
}
