<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Resources\Dynamic as DynamicResource;
use App\Repositories\Contracts\DynamicRepository;

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
     * @param Request $request 请求
     *
     * @return \Illuminate\Http\Resources\Json\Resource
     */
    public function index(Request $request)
    {
        $offset = $request->input('offset', hashids_encode(0));
        $offset = hashids_decode($offset);

        $dynamics = $this->guard()->check()
            ? $this->repository->forUser($this->guard()->id(), $offset)
            : $this->repository->defaults($offset);

        return respond()->resource(DynamicResource::collection($dynamics));
    }

    /**
     * 转发
     *
     * @param Request $request 请求
     * @param string $id ID
     *
     * @return \Illuminate\Http\Response
     */
    public function repost(Request $request, $id)
    {
        $this->validate($request, [
            'comment' => 'max:500'
        ]);

        $user_id = $this->guard()->id();
        $id      = hashids_decode($id);
        $comment = $request->input('comment', '');

        $this->repository->repost($user_id, $id, (string) $comment);

        return respond()->throw(205);
    }

    /**
     * 动态详情
     *
     * @param string $id ID
     *
     * @return \Illuminate\Http\Resources\Json\Resource
     */
    public function show($id)
    {
        $id = hashids_decode($id);

        $this->repository->with([
            'author', 'referer', 'dynamic.shareable', 'dynamic.author'
        ]);

        if ($this->guard()->check()) {
            $this->repository->withCount(['fabulous as is_fabulous' => function ($query) {
                $query->where('user_id', $this->guard()->id());
            }]);
        }

        $dynamic = $this->repository->find($id);

        return respond()->resource(new DynamicResource($dynamic));
    }

    /**
     * 赞
     *
     * @param Request $request 请求
     * @param string $id ID
     *
     * @return \Illuminate\Http\Response
     */
    public function addFabulous(Request $request, $id)
    {
        $id   = hashids_decode($id);
        $type = $request->input('type', 1);

        $this->repository->addFabulous($this->guard()->id(), $id, $type);

        return respond()->throw(205);
    }

    /**
     * 取消赞
     *
     * @param string $id ID
     *
     * @return \Illuminate\Http\Response
     */
    public function delFabulous($id)
    {
        $id = hashids_decode($id);

        $this->repository->delFabulous($this->guard()->id(), $id);

        return respond()->throw(205);
    }
}
