<?php

use Illuminate\Database\Seeder;

class LabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $labels = collect([
            [
                'name'  => 'Старая регистрация',
            ],
            [
                'name'  => 'Не говорит по русски',
            ],
            [
                'name'  => 'Не резидент',
            ],
        ]);

        $labels->each(function ($label) {
            try {
                \App\CRM\Label::forceCreate($label);
            } catch (Throwable $e) {
                $this->command->info('Can not create labels.');
            }
        });
    }
}
