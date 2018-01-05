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
