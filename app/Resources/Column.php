<?php

namespace App\Resources;

use Auth;
use App\Resources\Traits\UserRelatedTrait;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Http\Resources\Json\Resource;

class Column extends Resource
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
            'id'           => hashids_encode($this->id),
            'name'         => $this->name,
            'icon'         => $this->icon,
            'link'         => $this->link,
            'owner'        => $this->whenLoadedOwner(),
            'summary'      => $this->summary,
            'role'         => $this->whenLoadedRole(),
            'member_count' => $this->member_count,
            'created_at'   => (string)$this->created_at,
            'updated_at'   => (string)$this->updated_at
        ];
    }

    /**
     * 如果已加载 member 数据，添加角色信息
     *
     * @return MissingValue|mixed
     */
    protected function whenLoadedRole()
    {
        $members = $this->whenLoaded('members');

        if ($members instanceof MissingValue) {
            return $members;
        }

        $member = $members->first(function ($member) {
            return $member->id == Auth::guard()->id();
        });

        return $member->pivot->role;
    }

    /**
     * 如果存在 Owner 则加载 Owner 相关数据
     *
     * @return array|MissingValue|mixed
     */
    protected function whenLoadedOwner()
    {
        $owner = $this->whenLoaded('owner');

        return $this->withUser($owner);
    }
}