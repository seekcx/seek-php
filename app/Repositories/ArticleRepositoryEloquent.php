<?php

namespace App\Repositories;

use App\Entities\Article;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Repositories\Contracts\ArticleRepository;

class ArticleRepositoryEloquent extends BaseRepository implements ArticleRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Article::class;
    }
}
