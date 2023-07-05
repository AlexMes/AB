<?php

use Illuminate\Database\Seeder;

class LocalUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @throws \Throwable
     *
     * @return void
     */
    public function run()
    {
        throw_if(
            app()->environment('production'),
            Exception::class,
            ['You cant run this seeder in production environment']
        );

        try {
            \App\User::create([
                'name'     => 'Developer',
                'password' => 'password',
                'role'     => \App\User::ADMIN,
                'email'    => 'dev@dev.to'
            ]);
        } catch (Throwable $exception) {
            // zero fucks given
        }
    }
}
