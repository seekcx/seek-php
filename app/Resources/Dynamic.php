<?php

namespace App\Resources;

use App\Resources\Traits\UserRelatedTrait;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Http\Resources\Json\Resource;
use App\Entities\Dynamic\Flow as DynamicFlow;

class Dynamic extends Resource
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
        $original_author = $this->when($this->isRepost(), $this->withUser($this->dynamic->author));

        return [
            'id'              => hashids_encode($this->id),
            'author'          => $this->withUser($this->author),
            'original_author' => $original_author,
            'type'            => $this->type,
            'category'        => $this->dynamic->type,
            'content'         => $this->parseContent(),
            'context'         => $this->parseContext(),
            'repost_count'   => $this->repost_count,
            'comment_count'   => $this->comment_count,
            'fabulous_count'  => $this->fabulous_count,
            'created_at'      => (string)$this->created_at,
            'updated_at'      => (string)$this->updated_at,
        ];
    }

    /**
     * 是否是转发动态
     *
     * @return bool
     */
    protected function isRepost()
    {
        return $this->type == DynamicFlow::TYPE_REPOST;
    }

    /**
     * 解析内容
     *
     * @return string
     */
    protected function parseContent()
    {
        if ($this->isRepost()) {
            return $this->content;
        }

        switch ($this->dynamic->type) {
            case 'topic.create':
                return '创建了话题';
            case 'column.create':
                return '创建了专栏';
            default:
                return $this->content;
        }
    }

    /**
     * 解析上下文
     *
     * @return array|MissingValue
     */
    protected function parseContext()
    {
        switch ($this->dynamic->type) {
            case 'topic.create':
                return $this->parseTopic();
            case 'column.create':
                return $this->parseColumn();
            default:
                return new MissingValue;
        }
    }

    /**
     * 解析话题
     *
     * @return array
     */
    protected function parseTopic()
    {
        $shareable = $this->dynamic->shareable;

        return [
            'id'            => hashids_encode($shareable->id),
            'name'          => $shareable->name,
            'icon'          => $shareable->icon,
            'summary'       => $shareable->summary,
            'user_count'    => $shareable->user_count,
            'article_count' => $shareable->article_count,
            'column_count'  => $shareable->column_count,
        ];
    }

    /**
     * 解析专栏
     *
     * @return array
     */
    protected function parseColumn()
    {
        $shareable = $this->dynamic->shareable;

        return [
            'id'            => hashids_encode($shareable->id),
            'name'          => $shareable->name,
            'icon'          => $shareable->icon,
            'link'          => $shareable->link,
            'summary'       => $shareable->summary,
            'member_count'  => $shareable->member_count,
            'article_count' => $shareable->article_count
        ];
    }
}