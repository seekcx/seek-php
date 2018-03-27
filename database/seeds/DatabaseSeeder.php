<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(TopicSeeder::class);
        $this->call(ColumnSeeder::class);
        $this->call(DynamicSeeder::class);
        $this->call(ArticleSeeder::class);
    }
}
