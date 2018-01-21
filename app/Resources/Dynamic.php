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
        return [
            'id'             => hashids_encode($this->id),
            'author'         => $this->withUser($this->author),
            'referer'        => $this->whenLoadedReferer(),
            'original'       => $this->whenLoadedOriginal(),
            'type'           => $this->type,
            'category'       => $this->dynamic->type,
            'content'        => $this->parseContent(),
            'context'        => $this->parseContext(),
            'repost_count'   => $this->repost_count,
            'comment_count'  => $this->comment_count,
            'fabulous_count' => $this->fabulous_count,
            'created_at'     => (string)$this->created_at,
            'updated_at'     => (string)$this->updated_at,
        ];
    }

    protected function whenLoadedOriginal()
    {
        if (!$this->isRepost()) {
            return new MissingValue;
        }

        $dynamic = $this->whenLoaded('dynamic');

        $has    = array_key_exists('author', $dynamic->toArray());
        $author = $this->when($has, function () use ($dynamic) {
            return $this->withUser($dynamic->author);
        });

        return [
            'id'         => hashids_encode($dynamic->id),
            'author'     => $author,
            'type'       => $dynamic->type,
            'content'    => $this->parseType($dynamic->type, $dynamic->content),
            'created_at' => (string)$dynamic->created_at
        ];
    }

    protected function whenLoadedReferer()
    {
        $referer = $this->whenLoaded('referer');

        if ($referer instanceof MissingValue or !$referer) {
            return new MissingValue;
        }

        return [
            'id'             => hashids_encode($referer->id),
            'author'         => $this->withUser($this->referer->author),
            'content'        => $referer->content,
            'repost_count'   => $referer->repost_count,
            'comment_count'  => $referer->comment_count,
            'fabulous_count' => $referer->fabulous_count
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
     * 通过类型解析内容
     *
     * @param string $type    类型
     * @param string $default 默认
     *
     * @return string
     */
    protected function parseType($type, $default = '')
    {
        switch ($type) {
            case 'topic.create':
                return '创建了话题';
            case 'column.create':
                return '创建了专栏';
            default:
                return $default;
        }
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

        return $this->parseType($this->dynamic->type, $this->content);
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
