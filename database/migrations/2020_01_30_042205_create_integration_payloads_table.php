<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntegrationPayloadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('integration_payloads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('form_id')->index();
            $table->unsignedBigInteger('offer_id')->index();
            $table->unsignedBigInteger('lead_id')->index();
            $table->unsignedBigInteger('external_lead_id')->nullable()->default(null)->index();
            $table->string('requestUrl')->nullable()->default(null);
            $table->jsonb('requestContents')->nullable()->default(null);
            $table->smallInteger('responseStatus')
                ->nullable()
                ->default(null)
                ->index();
            $table->json('responseHeaders')
                ->nullable()
                ->default(null);
            $table->text('responseContents')->nullable()->default(null);
            $table->string('status')
                ->nullable()
                ->default(null)
                ->index();
            $table->jsonb('data')->nullable()->default(null);
            $table->string('runtime')->nullable()->default(null);
            $table->dateTime('sent_at')->nullable()->default(null);
            $table->dateTime('failed_at')->nullable()->default(null);
            $table->timestamps();

            $table->foreign('form_id')
                ->references('id')
                ->on('itegrations_forms')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('offer_id')
                ->references('id')
                ->on('offers')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('lead_id')
                ->references('id')
                ->on('leads')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
