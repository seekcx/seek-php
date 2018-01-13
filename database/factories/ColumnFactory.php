<?php

/**
 * 专栏填充数据
 *
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Entities\Column;

$factory->define(Column::class, function ($faker) {
    $faker_tw = Faker\Factory::create('zh_TW');

    $name = $faker_tw->unique()->name;
    $icon = sprintf(
        'https://dummyimage.com/128x128/eee/555.png&text=%s',
        $faker->randomLetter
    );

    $seed = rand(0, 5);

    return [
        'name'       => $name,
        'link'       => $faker->unique()->name,
        'founder_id' => rand(1, 100),
        'owner_id'   => rand(1, 100),
        'icon'       => $icon,
        'summary'    => $faker_tw->realText(rand(30, 150)),
        'created_ip' => $seed ? $faker->ipv4 : $faker->ipv6,
        'updated_ip' => $seed ? $faker->ipv4 : $faker->ipv6,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        'state'      => 1
    ];
});

