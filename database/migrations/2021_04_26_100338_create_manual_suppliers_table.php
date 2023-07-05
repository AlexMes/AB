<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManualSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        collect([
            'User 1',
            'User 2',
            'User 4',
            'User 31',
            'FARM MONO',
            'FARM AQUA',
        ])->each(fn ($name) => \App\ManualSupplier::create(['name' => $name]));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manual_suppliers');
    }
}
