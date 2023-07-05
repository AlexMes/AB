<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accesses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('received_at');
            $table->string('name')->nullable()->default(null);
            $table->unsignedBigInteger('user_id')->nullable()->default(null)->index();
            $table->unsignedBigInteger('supplier_id')->nullable()->default(null)->index();
            $table->string('type')->index();
            $table->string('facebook_url')->nullable()->default(null);
            $table->string('fbId')->nullable()->default(null);
            $table->string('phone')->nullable()->default(null);
            $table->text('password')->nullable()->default(null);
            $table->string('email')->nullable()->default(null);
            $table->text('email_password')->nullable()->default(null);
            $table->date('birthday')->nullable()->default(null);
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('supplier_id')
                ->references('id')
                ->on('access_suppliers')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
