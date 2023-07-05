<?php

use Illuminate\Database\Seeder;

class ManualTrafficSource extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $trafficSources = collect([
            [
                ['name'  => 'Unity'],
                ['default' => true]
            ],
            [
                ['name'  => 'Kadam'],
                ['default' => true]
            ],
            [
                ['name'  => 'Bigo'],
                ['default' => true]
            ],
            [
                ['name'  => 'Yandex'],
                ['default' => true]
            ],
            [
                ['name'  => 'VK'],
                ['default' => true]
            ],
            [
                ['name'  => 'Youtube'],
                ['default' => true]
            ],
            [
                ['name'  => 'Telegram'],
                ['default' => true]
            ],
            [
                ['name'  => 'Facebook'],
                ['default' => true]
            ],
            [
                ['name'  => 'Mintegral'],
                ['default' => true]
            ]
        ]);

        $trafficSources->each(function ($trafficSource) {
            try {
                \App\ManualTrafficSource::firstOrCreate($trafficSource[0], $trafficSource[1]);
            } catch (Throwable $e) {
                $this->command->info('Can not create deluge traffic source.');
            }
        });
    }
}
