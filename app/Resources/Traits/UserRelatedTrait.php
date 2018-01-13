<?php

namespace App\Resources\Traits;

trait UserRelatedTrait
{
    /**
     * 对与用户有关的数据进行处理
     *
     * @param mixed $user
     *
     * @return array
     */
    protected function withUser($user)
    {
        return [
            'id'     => hashids_encode($user->id),
            'name'   => $user->name,
            'avatar' => $user->avatar
        ];
    }
}