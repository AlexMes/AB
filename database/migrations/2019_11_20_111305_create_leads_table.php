<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('domain')
                ->nullable()
                ->default(null)
                ->index();
            $table->string('firstname')
                ->nullable()
                ->default(null)
                ->index();
            $table->string('lastname')
                ->nullable()
                ->default(null)
                ->index();
            $table->string('email')
                ->nullable()
                ->default(null)
                ->index();
            $table->string('phone')
                ->nullable()
                ->default(null)
                ->index();
            $table->string('ip')
                ->nullable()
                ->default(null)
                ->index();
            $table->string('form_type')
                ->nullable()
                ->default(null)
                ->index();
            $table->string('utm_source')
                ->nullable()
                ->default(null)
                ->index();
            $table->string('utm_content')
                ->nullable()
                ->default(null)
                ->index();
            $table->string('utm_campaign')
                ->nullable()
                ->default(null)
                ->index();
            $table->string('utm_term')
                ->nullable()
                ->default(null)
                ->index();
            $table->string('utm_medium')
                ->nullable()
                ->default(null)
                ->index();
            $table->string('clickid')
                ->nullable()
                ->default(null)
                ->index();
            $table->json('requestData')
                ->nullable()
                ->default(null);
            $table->timestamps();
        });
    }
}
