<?php

namespace App\Repositories;

use DB;
use App\Entities\Dynamic;
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

    public function forUser($user_id, $offset = 0, $limit = 15)
    {

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
        $builder = $this->model->with(['author', 'dynamic.author', 'dynamic.shareable']);

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
     * @param integer $id 动态 ID
     * @param string $comment 评论
     *
     * @return Dynamic\Flow
     */
    public function repost($user_id, $id, $comment = '')
    {
        $flow = $this->with(['dynamic', 'author'])->find($id);

        if (!$flow or $flow->dynamic->state == Dynamic::STATE_REMOVE) {
            abort(404, '动态不存在或已被删除');
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
}
