<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBranchIdAndDescriptionToOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->foreignId('branch_id')
                ->nullable()
                ->index()
                ->default(null)
                ->constrained('branches')
                ->onUpdate('cascade')
                ->onDelete('set null');
            $table->text('description')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn('branch_id');
            $table->dropColumn('description');
        });
    }
}
