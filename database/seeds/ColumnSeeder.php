<?php

use App\Entities\Column;
use Illuminate\Database\Seeder;

class ColumnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Column::class, 20)->create()->each(function ($column) {
            $column->topics()->attach([1, 2, 3, 4, 5]);

            $column->members()->attach($column->founder_id, [
                'role' => Column::ROLE_OWNER
            ]);
        });
    }
}
