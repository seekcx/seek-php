<?php

namespace App\Repositories;

use App\Entities\Topic;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Repositories\Contracts\TopicRepository;
use App\Repositories\Criteria\AvailableCriteria;

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
     * 启动
     *
     */
    public function boot()
    {
        $this->pushCriteria(AvailableCriteria::class);
    }
}
