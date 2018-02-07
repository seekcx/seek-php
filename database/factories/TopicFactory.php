<?php

/**
 * 话题填充数据
 *
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Entities\Topic;

$factory->define(Topic::class, function ($faker) {
    $seed = rand(0, 5);

    return [
        'name'       => $faker->unique()->name,
        'founder_id' => rand(1, 30),
        'icon'       => 'https://source.unsplash.com/random/240x240',
        'summary'    => $faker->realText(rand(30, 150)),
        'created_ip' => $seed ? $faker->ipv4 : $faker->ipv6,
        'updated_ip' => $seed ? $faker->ipv4 : $faker->ipv6,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        'state'      => 1
    ];
});