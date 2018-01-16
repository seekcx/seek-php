<?php

/**
 * 专栏填充数据
 *
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Entities\Column;

$factory->define(Column::class, function ($faker) {
    $faker_tw = Faker\Factory::create('zh_TW');

    $seed = rand(0, 5);

    return [
        'name'       => $faker_tw->unique()->name,
        'link'       => $faker->unique()->name,
        'founder_id' => rand(1, 100),
        'owner_id'   => rand(1, 100),
        'icon'       => 'https://source.unsplash.com/random/240x240',
        'summary'    => $faker_tw->realText(rand(30, 150)),
        'created_ip' => $seed ? $faker->ipv4 : $faker->ipv6,
        'updated_ip' => $seed ? $faker->ipv4 : $faker->ipv6,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        'state'      => 1
    ];
});

