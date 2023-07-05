<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResellBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resell_batches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamp('registered_at')->nullable()->default(null);
            $table->json('filters')->default('[]');
            $table->string('status')->default(\App\ResellBatch::PENDING);
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
        Schema::dropIfExists('resell_batches');
    }
}
