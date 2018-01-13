<?php

namespace App\Repositories;

use App\Entities\Topic;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Repositories\Contracts\TopicRepository;

class TopicRepositoryEloquent extends BaseRepository implements TopicRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Topic::class;
    }

    /**
     * 添加关注
     *
     * @param integer $user_id 用户ID
     * @param integer $id 话题ID
     */
    public function attachFollow($user_id, $id)
    {

    }
}
