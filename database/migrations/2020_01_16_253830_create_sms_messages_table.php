<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsMessagesTable extends Migration
{


    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('campaign_id')
                ->nullable()
                ->default(null)
                ->index();
            $table->unsignedBigInteger('lead_id')
                ->nullable()
                ->default(null)
                ->index();
            $table->string('external_id')
                ->nullable()
                ->default(null)
                ->index();

            $table->string('phone')->nullable()->default(null);
            $table->string('message')->nullable()->default(null);
            $table->string('status')->nullable()->default(null);
            $table->json('raw_response')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('campaign_id')
                ->references('id')
                ->on('sms_campaigns')
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
