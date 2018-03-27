<?php

namespace App\Resources;

use App\Resources\Traits\UserRelatedTrait;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Http\Resources\Json\Resource;

class Article extends Resource
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
            'id' => hashids_encode($this->id),
            'title' => $this->title,
            'author' => $this->whenLoadedAuthor()
        ];
    }

    /**
     * 如果存在 Author 则加载 Author 相关数据
     *
     * @return array|MissingValue|mixed
     */
    protected function whenLoadedAuthor()
    {
        $author = $this->whenLoaded('author');

        return $this->withUser($author);
    }
}