<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBundlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bundles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('offer_id')->nullable()->default(null);
            $table->string('utm_campaign')->nullable()->default(null);
            $table->text('examples')->nullable()->default(null);

            $table->string('geo')->nullable()->default(null);
            $table->unsignedTinyInteger('age')->nullable()->default(null);
            $table->string('gender')->nullable()->default(null);
            $table->text('interests')->nullable()->default(null);

            $table->string('device')->nullable()->default(null);
            $table->string('platform')->nullable()->default(null);

            $table->string('ad_id')->nullable()->default(null);

            $table->string('prelend_link')->nullable()->default(null);
            $table->string('lend_link')->nullable()->default(null);

            $table->string('utm_source')->nullable()->default(null);
            $table->string('utm_content')->nullable()->default(null);
            $table->string('utm_term')->nullable()->default(null);
            $table->string('utm_medium')->nullable()->default(null);

            $table->timestamps();

            $table->foreign('offer_id')
                ->on('offers')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->foreign('ad_id')
                ->on('facebook_ads')
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
        Schema::dropIfExists('bundles');
    }
}
