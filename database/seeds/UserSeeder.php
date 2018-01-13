<?php

use App\Entities\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = factory(User::class)->create([
            'name'   => '谢东',
            'email'  => 'abel@seek.cx',
            'mobile' => '18206750375'
        ]);

        factory(User\Stat::class)->create([
            'user_id' => $user->id
        ]);

        factory(User::class, 20)->create()->each(function ($user) {
            factory(User\Stat::class)->create([
                'user_id' => $user->id
            ]);
        });
    }
}
