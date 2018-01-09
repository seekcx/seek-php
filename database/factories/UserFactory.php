<?php
/**
 * 用户填充数据
 *
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Entities\User;

$factory->define(User::class, function ($faker) {
    $avatar = sprintf(
        'https://dummyimage.com/128x128/eee/555.png&text=%s',
        $faker->randomLetter
    );

    return [
        'name'         => $faker->unique()->name,
        'mobile'       => $faker->phoneNumber,
        'email'        => $faker->unique()->email,
        'password'     => 'abel1994',
        'gender'       => rand(0, 2),
        'birthday'     => $faker->date,
        'summary'      => 'a seeker',
        'introduction' => $faker->text(rand(10, 20)),
        'avatar'       => $avatar,
        'region_id'    => 10000
    ];
});

$factory->define(User\Stat::class, function ($faker) {
    $seed = rand(0, 5);

    return [
        'followers'      => rand(1, 200),
        'following'      => rand(1, 200),
        'topic'          => rand(1, 200),
        'group'          => rand(1, 200),
        'column'         => rand(1, 200),
        'register_ip'    => $seed ? $faker->ipv4 : $faker->ipv6,
        'register_at'    => $faker->dateTime,
        'last_login_ip'  => $seed ? $faker->ipv4 : $faker->ipv6,
        'last_login_at'  => $faker->dateTime,
        'last_active_ip' => $seed ? $faker->ipv4 : $faker->ipv6,
        'last_active_at' => $faker->dateTime,
        'login_count'    => rand(1, 200),
        'active_count'   => rand(1, 500)
    ];
});

$factory->define(User\Ship::class, function ($faker) {
    return [];
});