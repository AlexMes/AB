<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeAdIdInBundlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bundles', function (Blueprint $table) {
            $table->dropForeign('bundles_ad_id_foreign');
            $table->dropColumn('ad_id');

            $table->string('ad')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bundles', function (Blueprint $table) {
            $table->string('ad_id')->nullable()->default(null);
            $table->foreign('ad_id')
                ->on('facebook_ads')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->dropColumn('ad');
        });
    }
}
