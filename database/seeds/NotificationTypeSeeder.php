<?php

use App\NotificationType;
use Illuminate\Database\Seeder;

class NotificationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect(array_keys(NotificationType::NOTIFICATIONS))
            ->diff(NotificationType::pluck('key'))
            ->each(function ($key) {
                $type = NotificationType::create([
                    'key'   => $key,
                    'name'  => NotificationType::NOTIFICATIONS[$key],
                ]);

                if ($key === NotificationType::DESTINATION_DEPOSIT) {
                    $type->users()->attach(\App\User::get('id'));
                }
            });
    }
}
