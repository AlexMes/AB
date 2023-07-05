<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToGambleInsightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gamble_insights', function (Blueprint $table) {
            $table->foreignId('google_app_id')->index()->nullable()->default(null)
                ->constrained('google_apps')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('pour_type')->default('manual');
            $table->string('target')->nullable()->default(null);
            $table->unsignedInteger('sales')->default(0);
            $table->unsignedDecimal('deposit_sum')->default(0);
            $table->unsignedInteger('deposit_cnt')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gamble_insights', function (Blueprint $table) {
            $table->dropColumn('google_app_id');
            $table->dropColumn('pour_type');
            $table->dropColumn('target');
            $table->dropColumn('sales');
            $table->dropColumn('deposit_sum');
            $table->dropColumn('deposit_cnt');
        });
    }
}
