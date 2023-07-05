<?php

use Illuminate\Database\Migrations\Migration;

class DropUnqieFromAdsAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            DB::statement('ALTER TABLE "facebook_ads_accounts" DROP CONSTRAINT "facebook_ads_accounts_account_id_unique"');
        } catch (Throwable $exception) {
            // Too much fucking pressure, dont give a fuck.
            // in case this will make me cry
            Log::warning($exception->getMessage());
        }
    }
}
