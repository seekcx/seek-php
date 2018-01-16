<?php

namespace App\Repositories;

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
        return Dynamic::class;
    }

    public function forUser($user_id)
    {

    }

    /**
     * 获取默认动态
     *
     * @param integer [$page=1] 分页
     * @param integer [$limit=15] 每页数量
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    public function defaults($page = 1, $limit = 15)
    {
        $dynamics = Dynamic\Flow::with(['author', 'dynamic.shareable', 'dynamic.author'])
            ->orderBy('updated_at', 'desc')
            ->take($limit)
            ->skip(($page - 1) * $limit)
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
     * @return
     */
    public function forward($user_id, $id, $comment = '')
    {
        $dynamic = $this->find($id);

        if (!$dynamic or $dynamic->state == Dynamic::STATE_REMOVE) {
            abort(404, '动态不存在或已被删除');
        }

        $dynamicFlow = Dynamic\Flow::create([
            'dynamic_id' => $dynamic->id,
            'author_id'  => $user_id,
            'type'       => Dynamic\Flow::TYPE_REPOST,
            'content'    => $comment,
        ]);

        return $dynamicFlow;
    }
}
