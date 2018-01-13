<?php

namespace App\Resources\Traits;

use Illuminate\Http\Resources\MissingValue;

trait UserRelatedTrait
{
    /**
     * 对与用户有关的数据进行处理
     *
     * @param mixed $user
     *
     * @return array|MissingValue
     */
    protected function withUser($user)
    {
        if ($user instanceof MissingValue) {
            return $user;
        }

        if (empty($user)) {
            return new MissingValue;
        }

        return [
            'id'     => hashids_encode($user->id),
            'name'   => $user->name,
            'avatar' => $user->avatar
        ];
    }
}