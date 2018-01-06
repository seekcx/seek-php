<?php

namespace App\Http\Controllers\Auth;

use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;

class CredentialsController extends Controller
{
    /**
     * JWT Auth
     *
     * @var JWTAuth
     */
    protected $auth = null;

    /**
     * 构造器
     *
     * @param JWTAuth $auth
     */
    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * 创建会话
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'mobile' => ['required', 'phone:CN'],
            'secret' => 'required'
        ]);

        $credentials = [
            'mobile'   => $request->input('mobile'),
            'password' => $request->input('secret')
        ];

        try {
            if (!$token = $this->auth->attempt($credentials)) {
                abort(401, '手机号或密码不匹配');
            }

            return respond()->auth($token);
        } catch (JWTException $e) {
            abort(401, '无法生成登录凭据');
        }
    }
}