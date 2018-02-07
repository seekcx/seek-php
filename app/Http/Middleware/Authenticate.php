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
     * @param  string $mode 模式（loose：宽松、force：强制）
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $mode = 'loose')
    {

        try {
            $this->checkForToken($request);

            if ($this->auth->parseToken()->authenticate()) {
                return $next($request);
            }

            if ('force' == $mode) {
                throw new UnauthorizedHttpException('auth', '还未登录，请先登录');
            }
        } catch (TokenExpiredException $exception) {
            $token = $this->refresh();

            return $this->setAuthenticationHeader($next($request), $token);
        } catch (JWTException $exception) {
            if ('force' == $mode) {
                throw $exception;
            }

            return $next($request);
        } catch (UnauthorizedHttpException $exception) {
            if ('force' == $mode) {
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
}