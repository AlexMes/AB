<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBranchIdToSmsCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sms_campaigns', function (Blueprint $table) {
            $table->foreignId('branch_id')
                ->nullable()
                ->default(null)
                ->index()
                ->constrained('branches')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sms_campaigns', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
    }
}
