<?php

namespace App\Repositories;

use DB;
use App\Entities\Column;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Repositories\Contracts\ColumnRepository;

class ColumnRepositoryEloquent extends BaseRepository implements ColumnRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Column::class;
    }

    /**
     * 添加专栏
     *
     * @param integer $user_id 用户ID
     * @param string  $name    专栏名称
     * @param string  $link    链接
     * @param string  $summary 专栏描述
     * @param array   $topics  话题
     *
     * @return Column
     */
    public function add($user_id, $name, $link, $summary, array $topics)
    {
        return DB::transaction(function () use ($user_id, $name, $link, $summary, $topics) {
            $column = $this->create([
                'name'       => $name,
                'link'       => $link,
                'summary'    => $summary,
                'founder_id' => $user_id,
                'owner_id'   => $user_id,
            ]);

            $column->topics()->attach($topics);

            $column->members()->attach($user_id, [
                'role' => Column::ROLE_OWNER
            ]);

            return $column;
        });
    }
}
