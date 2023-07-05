<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDesignerIdToFacebookAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('facebook_ads', function (Blueprint $table) {
            $table->unsignedBigInteger('designer_id')->nullable()->default(null)->index();

            $table->foreign('designer_id')
                ->on('designers')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('facebook_ads', function (Blueprint $table) {
            $table->dropColumn('designer_id');
        });
    }
}
