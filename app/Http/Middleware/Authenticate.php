<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Authenticate extends BaseMiddleware
{
    /**
     * 处理进入的请求
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string $force 是否强制
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $force = 'no-force')
    {
        if ($this->isForce($force)) {
            $this->checkForToken($request);
        }

        try {
            if ($this->auth->parseToken()->authenticate()) {
                return $next($request);
            }

            if ($this->isForce($force)) {
                throw new UnauthorizedHttpException('auth', '还未登录，请先登录');
            }
        } catch (TokenExpiredException $exception) {
            $token = $this->refresh();

            return $this->setAuthenticationHeader($next($request), $token);
        } catch (JWTException $exception) {
            if ($this->isForce($force)) {
                throw $exception;
            }

            return $next($request);
        }
    }

    /**
     * 刷新已过期的 JWT Token
     *
     * @return string
     */
    protected function refresh()
    {
        try {
            $token = $this->auth->refresh();

            Auth::guard(config('auth.defaults.guard', 'api'))
                ->onceUsingId($this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray()['sub']);
        } catch (JWTException $exception) {
            throw new UnauthorizedHttpException('auth', $exception->getMessage());
        }

        return $token;
    }

    /**
     * 是否强制验证
     *
     * @param string $force 强制
     *
     * @return bool
     */
    protected function isForce($force)
    {
        return $force == 'force';
    }
}