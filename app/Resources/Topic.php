<?php

namespace App\Resources;

use App\Resources\Traits\UserRelatedTrait;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Http\Resources\Json\Resource;

class Topic extends Resource
{
    use UserRelatedTrait;

    /**
     * 转换为数组
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            => hashids_encode($this->id),
            'name'          => $this->name,
            'founder'       => $this->whenLoadedFounder(),
            'icon'          => $this->icon ?: 'https://static.seek.cx/topic/icon/default.png',
            'summary'       => $this->summary,
            'user_count'    => $this->user_count,
            'article_count' => $this->article_count,
            'column_count'  => $this->column_count
        ];
    }

    /**
     * 如果已加载 Founder，则加载相关数据
     *
     * @return array|MissingValue|mixed
     */
    protected function whenLoadedFounder()
    {
        $founder = $this->whenLoaded('founder');

        return $this->withUser($founder);
    }
}