<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Resources\Topic as TopicResource;
use App\Repositories\Contracts\TopicRepository;

class TopicController extends Controller
{
    /**
     * 仓库
     *
     * @var TopicRepository
     */
    protected $repository;

    /**
     * 构造器
     *
     * @param TopicRepository $repository
     */
    public function __construct(TopicRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 创建话题
     *
     * @param  Request $request
     *
     * @return \Illuminate\Http\Resources\Json\Resource
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'name'    => ['required', 'unique:topic'],
            'summary' => ['required', 'min:5']
        ]);

        $topic = $this->repository->create([
            'founder_id' => $request->user()->id,
            'name'       => $request->input('name'),
            'icon'       => $request->input('icon', ''),
            'summary'    => $request->input('summary'),
            'created_ip' => $request->ip(),
            'updated_ip' => $request->ip(),
            'state'      => 1
        ]);

        return $this->show($topic->id, false);
    }

    /**
     * 话题详情
     *
     * @param string|integer $id     ID
     * @param bool           $decode 是否解码
     *
     * @return \Illuminate\Http\Resources\Json\Resource
     */
    public function show($id, $decode = true)
    {
        $id = $decode ? hashids_decode($id) : $id;

        $topics = $this->repository
            ->with('founder')
            ->findWhere(['id' => $id]);

        if (!$topics->count()) {
            abort(404, '话题不存在或已被删除');
        }

        return respond()->resource(new TopicResource($topics->last()));
    }

    /**
     * 关注
     *
     * @param string $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function follow($id)
    {
        try {
            $this->repository
                ->find(hashids_decode($id))
                ->users()
                ->attach($this->guard()->id());
        } catch (QueryException $e) {
            abort(400, '你已关注过此话题了');
        }

        return respond()->throw(205);
    }

    /**
     * 取消关注
     *
     * @param string $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function unfollow($id)
    {
        $this->repository
            ->find(hashids_decode($id))
            ->users()
            ->detach($this->guard()->id());

        return respond()->throw(205);
    }
}