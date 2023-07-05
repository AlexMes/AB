<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('effectiveDate')->nullable()->default(null);
            $table->string('url')->nullable()->default(null);
            $table->string('status');
            $table->string('linkType')->default(\App\Domain::PRELANDING);
            $table->string('comment')->nullable()->default(null);
            $table->datetime('down_since')->nullable()->default(null);

            $table->unsignedBigInteger('user_id')->nullable()->default(null)->index();
            $table->unsignedBigInteger('order_id')->nullable()->default(null)->index();

            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
