<?php

namespace App\Rules\Topic;

use App\Entities\Topic;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Validation\Rule;


class IsAvailable implements Rule
{
    /**
     * 未通过
     *
     * @var Collection
     */
    protected $notPass = null;

    /**
     * 判断验证规则是否通过
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $ids = collect(explode(',', $value))
            ->filter(function ($id) {
                return !empty($id);
            })
            ->map('hashids_decode');

        $topics = Topic::whereIn('id', $ids)
            ->available()
            ->pluck('id');

        $this->notPass = $topics->diff($ids);

        return $this->notPass->count() == 0;
    }

    /**
     * 获取验证错误信息。
     *
     * @return string
     */
    public function message()
    {
        $topics = $this->notPass->map(function ($id) {
            return hashids_encode($id);
        });

        return sprintf('话题 %s 不存在或已不可用', $topics->implode('、'));
    }
}