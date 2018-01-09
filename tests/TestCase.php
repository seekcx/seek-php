<?php

use App\Entities\User;
use Tymon\JWTAuth\JWTAuth;

abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    public function createToken(User $user)
    {
        return app(JWTAuth::class)->fromUser($user);
    }
}
