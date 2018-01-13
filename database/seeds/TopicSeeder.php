<?php

use App\Entities\Topic;
use Illuminate\Database\Seeder;

class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Topic::class, 20)->create()->each(function ($topic) {
            $topic->followers()->attach([1, 2, 3, 4, 5]);
        });
    }
}
