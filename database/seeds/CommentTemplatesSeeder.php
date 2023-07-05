<?php

use Illuminate\Database\Seeder;

class CommentTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Facebook\CommentTemplate::create(['message' => 'testing']);
    }
}
