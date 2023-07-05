<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignmentDayToDaySnapshotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assignment_day_to_day_snapshots', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('manager_id')->index();
            $table->unsignedBigInteger('offer_id')->index();
            $table->string('manager');
            $table->string('offer');
            $table->unsignedSmallInteger('total')->default(0);
            $table->unsignedSmallInteger('deposits')->default(0);
            $table->unsignedSmallInteger('no_answer')->default(0);
            $table->timestamps();

            $table->unique(['date', 'manager_id', 'offer_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assignment_day_to_day_snapshots');
    }
}
