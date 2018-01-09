<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CredentialsController extends Controller
{
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

        if (!$token = $this->guard()->attempt($credentials)) {
            abort(401, '手机号或密码不匹配');
        }

        return respond()->auth($token);
    }

    /**
     * 刷新会话
     *
     * @return \Illuminate\Http\Response
     */
    public function refresh()
    {
        return respond()->auth($this->guard()->refresh());
    }

    /**
     * 注销
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $this->guard()->logout();

        return respond()->throw(205);
    }
}