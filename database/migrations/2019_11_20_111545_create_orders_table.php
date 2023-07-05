<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('links_count')->nullable()->default(1)->index();
            $table->string('cloak')->nullable()->default(null)->index();
            $table->integer('binom_id')->nullable()->default(null)->index();
            $table->string('linkType')->nullable()->default(null)->index();
            $table->string('registrar')->nullable()->default(null)->index();
            $table->boolean('useCloudflare')->default(false);
            $table->boolean('useConstructor')->default(false);

            $table->unsignedBigInteger('landing_id')->nullable()->default(null)->index();

            $table->timestamp('deadline_at')
                ->nullable()
                ->default(null)->index();

            $table->timestamps();
        });
    }
}
