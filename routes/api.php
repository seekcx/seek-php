<?php

/**
 * --------------------------------------------------------------------------
 * Application Routes
 * --------------------------------------------------------------------------
 *
 * @var \Laravel\Lumen\Routing\Router $router
 */

# 服务
$router->get('/', 'ServerController@index');
$router->get('ping', 'ServerController@ping');
$router->get('time', 'ServerController@time');

# 用户
$router->post('user', 'UserController@create');
$router->get('user/{id:[0-9a-f]{8}}', 'UserController@show');
$router->post('user/credentials', 'Auth\CredentialsController@create');

# 话题
$router->get('topic/{id:[0-9a-f]{8}}', 'TopicController@show');

# 需要登录
$router->group([
    'middleware' => ['jwt.auth']
], function () use ($router) {
    $router->get('user', 'UserController@show');
    $router->delete('user/credentials', 'Auth\CredentialsController@destroy');
    $router->put('user/credentials', 'Auth\CredentialsController@refresh');
    $router->post('user/{id:[0-9a-f]{8}}/followers', 'UserController@follow');
    $router->delete('user/{id:[0-9a-f]{8}}/followers', 'UserController@unfollow');

    # 话题
    $router->post('topic', 'TopicController@create');
    $router->post('topic/{id:[0-9a-f]{8}}/followers', 'TopicController@follow');
    $router->delete('topic/{id:[0-9a-f]{8}}/followers', 'TopicController@unfollow');
});