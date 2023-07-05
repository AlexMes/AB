<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantLeadStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tcrm_frx_tenant_lead_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')
                ->index()
                ->constrained('lead_order_assignments')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->boolean('exists')->default(false);
            $table->string('status')->nullable()->default(null);
            $table->string('result')->nullable()->default(null);
            $table->string('closer')->nullable()->default(null);
            $table->boolean('manager_can_view')->nullable()->default(null);
            $table->timestamp('last_called_at')->nullable()->default(null);
            $table->timestamp('last_viewed_at')->nullable()->default(null);
            $table->timestamp('last_updated_at')->nullable()->default(null);
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
        Schema::dropIfExists('tcrm_frx_tenant_lead_stats');
    }
}
