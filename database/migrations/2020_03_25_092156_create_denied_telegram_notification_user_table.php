<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeniedTelegramNotificationUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('denied_telegram_notification_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('notification_type_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->unique(['notification_type_id', 'user_id'], 'denied_tg_notif_users_notif_id_user_id_unique');

            $table->foreign('notification_type_id')
                ->on('notification_types')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->on('users')
                ->references('id')
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
        Schema::dropIfExists('denied_telegram_notification_user');
    }
}
