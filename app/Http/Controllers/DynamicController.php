<?php

namespace App\Http\Controllers;

use App\Resources\Dynamic as DynamicResource;
use App\Repositories\Contracts\DynamicRepository;
use Illuminate\Http\Request;

class DynamicController extends Controller
{
    /**
     * 仓库
     *
     * @var DynamicRepository
     */
    protected $repository;

    /**
     * 构造器
     *
     * @param DynamicRepository $repository
     */
    public function __construct(DynamicRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 动态
     *
     * @return \Illuminate\Http\Resources\Json\Resource
     */
    public function index()
    {
        $dynamics = $this->guard()->check()
            ? $this->repository->forUser($this->guard()->id())
            : $this->repository->defaults();

        return respond()->resource(DynamicResource::collection($dynamics));
    }

    public function repost(Request $request, $id)
    {
        $this->validate($request, [
            'comment' => ['max:500']
        ]);

        $user_id = $this->guard()->id();
        $id      = hashids_decode($id);
        $comment = $request->input('comment', '');

        $this->repository->forward($user_id, $id, $comment);
    }

    public function show($id)
    {

    }
}