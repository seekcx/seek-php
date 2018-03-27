<?php

use App\Entities\Dynamic;
use Illuminate\Database\Seeder;

class DynamicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($idx = 1; $idx <= 20; $idx++) {
            factory(Dynamic::class)->create([
                'shareable_id' => $idx
            ])->each(function ($dynamic) {
                $flow = factory(Dynamic\Flow::class)->create([
                    'dynamic_id' => $dynamic->id,
                    'author_id'  => $dynamic->author_id,
                    'type'       => Dynamic\Flow::TYPE_NORMAL
                ]);

                factory(Dynamic\Fabulous::class, rand(0, 20))->create([
                    'flow_id' => $flow->id,
                    'type'    => Dynamic\Fabulous::TYPE_LIKE
                ]);
            });
        }
    }
}