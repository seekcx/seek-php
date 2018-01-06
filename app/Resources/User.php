<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\MissingValue;

class User extends Resource
{
    /**
     * 转换为数组
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $user = $request->user();

        $this->whenLoaded('stat');

        return [
            'id'           => hashids_encode($this->id),
            'name'         => $this->name,
            'avatar'       => $this->avatar,
            $this->mergeWhen($user && $user->id == $this->id, [
                'mobile' => hide_mobile($this->mobile),
                'email'  => hide_email($this->email),
            ]),
            'gender'       => $this->gender,
            'birthday'     => $this->birthday,
            'summary'      => $this->summary,
            'introduction' => $this->introduction,
            'stats'        => $this->whenLoadedStat()
        ];
    }

    /**
     * 如果已加载 Stat，则添加 Stat 相关信息
     *
     * @return array|MissingValue
     */
    protected function whenLoadedStat()
    {
        $stat = $this->whenLoaded('stat');

        if ($stat instanceof MissingValue) {
            return $stat;
        }

        return [
            'followers'   => $stat->followers,
            'following'   => $stat->following,
            'topic'       => $stat->topic,
            'column'      => $stat->column,
            'group'       => $stat->group,
            'register_at' => (string) $stat->register_at
        ];
    }
}