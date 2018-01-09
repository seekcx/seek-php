<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;
use App\Resources\User as UserResource;
use App\Repositories\Contracts\UserRepository;

class UserController extends Controller
{
    /**
     * Repository
     *
     * @var UserRepository
     */
    protected $repository;

    /**
     * 构造器
     *
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 注册
     *
     * @param Request $request 请求
     * @param JWTAuth $auth    JWT Auth
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request, JWTAuth $auth)
    {
        $this->validate($request, [
            'name'     => ['required', 'name', 'unique:user,name'],
            'mobile'   => ['required', 'phone:CN', 'unique:user,mobile'],
            'password' => ['required', 'regex:/^.{6,20}$/'],
            'captcha'  => ['required', 'regex:/^\d{6}$/']
        ]);

        $user = $this->repository->register(
            $request->input('name'),
            $request->input('mobile'),
            $request->input('password')
        );

        return respond()->auth($auth->fromUser($user));
    }

    /**
     * 用户详情
     *
     * @param Request     $request 请求
     * @param string|null $id      目标用户 ID
     *
     * @return \Illuminate\Http\Resources\Json\Resource
     */
    public function show(Request $request, $id = null)
    {
        $user = $this->repository
            ->with('stat')
            ->find(is_null($id) ? $request->user()->id : hashids_decode($id));

        return respond()->resource(new UserResource($user));
    }

    /**
     * 关注
     *
     * @param string $id 关注的ID
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function follow($id)
    {
        $user = $this->guard()->user();

        $this->repository->follow(hashids_decode($id), $user->id);

        return respond()->throw(205);
    }

    /**
     * 取消关注
     *
     * @param string $id 关注的ID
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function unfollow($id)
    {
        $user = $this->guard()->user();

        $this->repository->unfollow(hashids_decode($id), $user->id);

        return respond()->throw(205);
    }
}