<?php

/**
 * 话题填充数据
 *
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Entities\Topic;

$factory->define(Topic::class, function ($faker) {
    $faker_tw = Faker\Factory::create('zh_TW');

    $name = $faker->unique()->name;
    $icon = sprintf(
        'https://dummyimage.com/128x128/eee/555.png&text=%s',
        $faker->randomLetter
    );

    $seed = rand(0, 5);

    return [
        'name'       => $name,
        'founder_id' => rand(1, 30),
        'icon'       => $icon,
        'summary'    => $faker_tw->realText(rand(30, 150)),
        'created_ip' => $seed ? $faker->ipv4 : $faker->ipv6,
        'updated_ip' => $seed ? $faker->ipv4 : $faker->ipv6,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        'state'      => 1
    ];
});