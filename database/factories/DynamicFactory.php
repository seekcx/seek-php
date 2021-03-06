<?php

/**
 * 动态填充数据
 *
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Entities\Dynamic;

$factory->define(Dynamic::class, function ($faker) {
    $seed = rand(0, 5);

    $type = collect([
        'topic.create', 'column.create'
    ])->random(1)->first();

    $shareable_type = collect([
        'topic', 'column'
    ])->random(1)->first();

    return [
        'author_id'      => rand(1, 20),
        'content'        => $faker->realText(rand(30, 150)),
        'type'           => $type,
        'shareable_id'   => rand(1, 20),
        'shareable_type' => $shareable_type,
        'created_ip'     => $seed ? $faker->ipv4 : $faker->ipv6,
        'updated_ip'     => $seed ? $faker->ipv4 : $faker->ipv6,
    ];
});

$factory->define(Dynamic\Flow::class, function ($faker) {

    return [
        'content'        => $faker->realText(rand(30, 150)),
        'comment_count'  => rand(0, 50),
        'fabulous_type'  => rand(0, 7),
        'fabulous_count' => rand(0, 50),
        'fabulous_user'  => rand(0, 20)
    ];
});

$factory->define(Dynamic\Fabulous::class, function ($faker) {
    return [
        'user_id' => rand(1, 20),
        'flow_id' => rand(1, 20)
    ];
});