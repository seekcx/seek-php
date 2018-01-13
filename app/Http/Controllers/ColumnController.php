<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Resources\Column as ColumnResource;
use App\Repositories\Contracts\ColumnRepository;
use App\Rules\Topic\IsAvailable as TopicIsAvailable;

class ColumnController extends Controller
{
    /**
     * 仓库
     *
     * @var ColumnRepository
     */
    protected $repository;

    /**
     * 构造器
     *
     * @param ColumnRepository $repository
     */
    public function __construct(ColumnRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 创建专栏
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Resources\Json\Resource
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'name'    => ['required', 'unique:column,name'],
            'link'    => ['required', 'min:4', 'unique:column,link'],
            'summary' => ['required', 'min:10'],
            'topics'  => ['required', new TopicIsAvailable]
        ]);

        $topics = $request->input('topics', '');

        $topics = collect(explode(',', $topics))
            ->reject('empty')
            ->map('hashids_decode')
            ->toArray();

        $column = $this->repository->add(
            $request->user()->id,
            $request->input('name'),
            $request->input('link'),
            $request->input('summary'),
            $topics
        );

        return $this->show($column->id, false);
    }

    /**
     * 展示详情
     *
     * @param mixed $id
     * @param bool [$decode=true] 是否解码
     *
     * @return \Illuminate\Http\Resources\Json\Resource
     */
    public function show($id, $decode = true)
    {
        $id = $decode ? hashids_decode($id) : $id;

        $relations = collect(['owner']);

        if ($user_id = $this->guard()->id()) {
            $relations->put('members', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            });
        }

        $column = $this->repository
            ->with($relations->toArray())
            ->find($id);

        return respond()->resource(new ColumnResource($column));
    }
}