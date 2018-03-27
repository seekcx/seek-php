<?php

/**
 * 文章填充数据
 *
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Entities\Article;

$factory->define(Article::class, function ($faker) {
    $seed = rand(0, 5);

    return [
        'author_id'  => rand(1, 20),
        'column_id'  => rand(1, 20),
        'title'      => $faker->realText(rand(20, 64)),
        'image'      => json_encode([
            'https://source.unsplash.com/random/980x560'
        ]),
        'summary'    => $faker->realText(rand(150, 450)),
        'content'    => $faker->realText(rand(450, 9000)),
        'edit_count' => rand(1, 50),
        'created_ip' => $seed ? $faker->ipv4 : $faker->ipv6,
        'updated_ip' => $seed ? $faker->ipv4 : $faker->ipv6,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        'state'      => 1
    ];
});
