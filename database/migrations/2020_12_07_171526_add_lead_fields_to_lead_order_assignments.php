<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeadFieldsToLeadOrderAssignments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_order_assignments', function (Blueprint $table) {
            $table->unsignedSmallInteger('gender_id')->nullable()->default(null);
            $table->string('external_id')->nullable()->default(null);
            $table->timestamp('called_at')->nullable()->default(null);
            $table->string('timezone')->nullable()->default(null);
            $table->string('age')->nullable()->default(null);
            $table->string('profession')->nullable()->default(null);
            $table->string('reject_reason')->nullable()->default(null);
            $table->string('status')->nullable()->default(null);
            $table->text('comment')->nullable()->default(null);
            $table->string('deposit_sum')->nullable()->default(null);
            $table->string('alt_phone')->nullable()->default(null);
            $table->decimal('benefit')->nullable()->default(null);
            $table->timestamp('callback_at')->nullable()->default(null);
            $table->string('frx_call_id')->nullable()->default(null);
            $table->string('frx_lead_id')->nullable()->default(null);
        });
    }
}
