<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Resources\Article as ArticleResource;
use App\Repositories\Contracts\ArticleRepository;

class ArticleController extends Controller
{
    /**
     * 仓库
     *
     * @var ArticleRepository
     */
    protected $repository;

    /**
     * 构造器
     *
     * @param ArticleRepository $repository
     */
    public function __construct(ArticleRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $condition = collect([]);

        if ($request->has('column')) {
            $condition->put('column_id', $request->input('column'));
        }

        if ($request->has('author')) {
            $condition->put('author_id', $request->input('author'));
        }

        $articles = $this->repository
            ->with(['author', 'column'])
            ->findWhere($condition->toArray());

        return respond()->resource(ArticleResource::collection($articles));
    }
}