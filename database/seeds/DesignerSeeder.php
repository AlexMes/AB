<?php

use App\Designer;
use Illuminate\Database\Seeder;

class DesignerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect(['andre', 'KA'])
            ->diff(Designer::pluck('name'))
            ->each(function ($name) {
                Designer::create([
                    'name'   => $name,
                ]);
            });
    }
}
