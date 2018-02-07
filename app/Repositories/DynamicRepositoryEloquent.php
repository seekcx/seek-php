<?php

namespace App\Repositories;

use DB;
use App\Entities\User;
use App\Entities\Dynamic;
use App\Repositories\Contracts\UserRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Repositories\Contracts\dynamicRepository;

class DynamicRepositoryEloquent extends BaseRepository implements DynamicRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Dynamic\Flow::class;
    }

    /**
     * 通过用户获取动态信息
     *
     * @param integer $user_id   用户ID
     * @param integer $offset_id 偏移
     * @param integer $limit     限制
     *
     * @return mixed
     */
    public function forUser($user_id, $offset_id = 0, $limit = 15)
    {
        $user_repository = app(UserRepository::class);

        $following = $user_repository->followingIdList($user_id);

        // 自己默认关注自己的动态
        $following[] = $user_id;

        $listen = [
            'topic'  => $user_repository->followTopicIdList($user_id),
            'column' => $user_repository->subscribeColumnIdList($user_id),
        ];

        $builder = $this->model->leftJoin('dynamic', 'dynamic.id', '=', 'dynamic_flow.id')
            ->where(function ($query) use ($listen, $following) {
                $query->where(function ($query) use ($following) {
                    $query->whereIn('dynamic_flow.author_id', $following);
                });

                foreach($listen as $object => $ids) {
                    $query->orWhere(function ($query) use ($object, $ids) {
                        $query->whereIn('shareable_id', $ids)
                            ->where('shareable_type', $object);
                    });
                }
            });

        $builder->with(['author', 'dynamic.author', 'fabulousUser', 'dynamic.shareable'])
            ->withCount(['fabulous as is_fabulous' => function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            }]);

        if ($offset_id != 0) {
            $builder->where('id', '<', $offset_id);
        }

        $dynamics = $builder->orderBy('dynamic_flow.created_at', 'desc')
            ->take($limit)
            ->get();

        return $dynamics;
    }

    /**
     * 获取默认动态
     *
     * @param integer [$offset_id=0] 偏移ID
     * @param integer [$limit=15] 每页数量
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    public function defaults($offset_id = 0, $limit = 15)
    {
        $builder = $this->model->with([
            'author', 'dynamic.author', 'fabulousUser', 'dynamic.shareable'
        ])->withCount('fabulous');

        if ($offset_id != 0) {
            $builder->where('id', '<', $offset_id);
        }

        $dynamics = $builder->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();

        return $dynamics;
    }

    /**
     * 转发
     *
     * @param integer $user_id 用户 ID
     * @param integer $id      动态 ID
     * @param string  $comment 评论
     *
     * @return Dynamic\Flow
     */
    public function repost($user_id, $id, $comment = '')
    {
        $flow = Dynamic\Flow::with(['dynamic', 'author'])->find($id);

        if (!$flow) {
            abort(404, '动态不存在');
        }

        if ($flow->state == Dynamic\Flow::STATE_REMOVE) {
            abort(404, '动态已被删除');
        }

        if ($flow->state == Dynamic\Flow::STATE_LOCKED) {
            abort(403, '动态已被锁定');
        }

        if ($flow->dynamic->state == Dynamic::STATE_LOCKED) {
            abort(403, '原始动态已被锁定');
        }

        if ($flow->dynamic->state == Dynamic::STATE_REMOVE) {
            abort(404, '原始动态已被删除');
        }

        // 如果转发的动态本身已经是转发，增加转发内容
        if ($flow->type == Dynamic\Flow::TYPE_REPOST) {
            $comment .= sprintf(' //@%s: %s', $flow->author->name, $flow->content);
        }

        $dynamic = DB::transaction(function () use ($flow, $user_id, $comment) {
            $flow->increment('repost_count');

            $dynamic = $this->create([
                'referer_id' => $flow->id,
                'dynamic_id' => $flow->dynamic->id,
                'author_id'  => $user_id,
                'type'       => Dynamic\Flow::TYPE_REPOST,
                'content'    => $comment
            ]);

            return $dynamic;
        });

        return $dynamic;
    }

    /**
     * 添加赞
     *
     * @param integer $user_id 用户 ID
     * @param integer $id      ID
     * @param integer $type    类型
     */
    public function addFabulous($user_id, $id, $type = 1)
    {
        $types = collect([
            Dynamic\Fabulous::TYPE_PRAISE,
            Dynamic\Fabulous::TYPE_LIKE,
            Dynamic\Fabulous::TYPE_SMILE,
        ]);

        if (!$types->contains($type)) {
            abort(400, '未知的类型');
        }

        $exists = Dynamic\Fabulous::where('user_id', $user_id)
            ->where('flow_id', $id)
            ->exists();

        if ($exists) {
            abort(409, '你已经赞过这个动态了');
        }

        DB::transaction(function () use ($user_id, $id, $type) {
            Dynamic\Fabulous::create([
                'user_id' => $user_id,
                'flow_id' => $id,
                'type'    => $type
            ]);

            $flow = Dynamic\Flow::find($id);

            $flow->increment('fabulous_count', 1, [
                'fabulous_user' => $user_id,
                'fabulous_type' => $flow->fabulous_type | $type
            ]);
        });
    }

    /**
     * 取消赞
     *
     * @param integer $user_id 用户 ID
     * @param integer $id      ID
     *
     * @return Dynamic\Fabulous
     */
    public function delFabulous($user_id, $id)
    {
        $fabulous = Dynamic\Fabulous::where('user_id', $user_id)
            ->where('flow_id', $id)
            ->first();

        if (!$fabulous) {
            abort(404, '你没有赞过这个动态');
        }

        DB::transaction(function () use ($fabulous, $id) {
            // 最后一个点赞的用户
            $user_id = Dynamic\Fabulous::where('flow_id', $id)
                ->where('user_id', '!=', $fabulous->user_id)
                ->orderBy('created_at', 'desc')
                ->value('user_id');

            $flow = Dynamic\Flow::where('id', $id)->first();

            $flow->decrement('fabulous_count', 1, [
                'fabulous_type' => $flow->fabulous_type ^ $fabulous->type,
                'fabulous_user' => (int)$user_id
            ]);

            $fabulous->delete();
        });
    }
}
