<?php

namespace App\Repositories\Contracts;

use App\Entities\User;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface UsersRepository
 *
 * @package namespace App\Repositories\Contracts;
 */
interface UserRepository extends RepositoryInterface
{
    /**
     * 注册
     *
     * @param string $name     用户名
     * @param string $mobile   手机号
     * @param string $password 密码
     *
     * @return User
     */
    public function register($name, $mobile, $password);
}
