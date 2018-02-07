<?php

namespace App\Repositories\Contracts;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface DynamicRepository
 * @package namespace App\Repositories\Contracts;
 */
interface DynamicRepository extends RepositoryInterface
{
    /**
     * 通过用户获取动态信息
     *
     * @param integer $user_id   用户ID
     * @param integer $offset_id 偏移
     * @param integer $limit     限制
     *
     * @return mixed
     */
    public function forUser($user_id, $offset_id = 0, $limit = 15);
}
