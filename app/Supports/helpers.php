<?php

use App\Http\Respond;
use Spatie\Regex\Regex;
use Hashids\HashidsException;
use Vinkla\Hashids\Facades\Hashids;

if (! function_exists('respond')) {
    /**
     * 获取一个 Respond 实例
     *
     * @return Respond
     */
    function respond()
    {
        return app(Respond::class);
    }
}

if (! function_exists('hashids_encode')) {
    /**
     * Hashids 编码
     *
     * @param  string  $id ID
     * @return string
     */
    function hashids_encode($id)
    {
        return Hashids::encode($id);
    }
}

if (! function_exists('hashids_decode')) {
    /**
     * Hashids 解码
     *
     * @param  string  $hash HASH
     * @return integer
     */
    function hashids_decode($hash)
    {
        if (! $playload = Hashids::decode($hash)) {
            throw new HashidsException('Invalid hashids');
        }

        return head($playload);
    }
}
if (! function_exists('hide_mobile')) {
    /**
     * 隐藏手机号
     *
     * @param  string  $mobile
     * @return string
     */
    function hide_mobile($mobile)
    {
        return empty($mobile) ? '' :
            substr($mobile, 0, 3) .'****' .substr($mobile, -4);
    }
}

if (! function_exists('is_email')) {

    /**
     * 判断给定字符串是否是一个邮箱
     *
     * @param string $string
     *
     * @return bool
     */
    function is_email($string) {
        return Regex::match('/^\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}$/', $string)
            ->hasMatch();
    }
}

if (! function_exists('hide_email')) {
    /**
     * 隐藏邮箱
     *
     * @param  string  $email
     * @return string
     */
    function hide_email($email)
    {
        if (!is_email($email)) {
            return $email;
        }

        list($name, $domain) = explode('@', $email);

        return sprintf('%s****@%s', substr($name, 0, 2), $domain);
    }
}