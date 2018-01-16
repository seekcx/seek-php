<?php

namespace App\Http;

use Auth;

class Respond
{
    /**
     * 资源响应
     *
     * @param \Illuminate\Http\Resources\Json\Resource $resource 资源
     *
     * @return \Illuminate\Http\Resources\Json\Resource
     */
    public function resource(\Illuminate\Http\Resources\Json\Resource $resource)
    {
        return $resource;
    }

    /**
     * 发送响应
     *
     * @param  string $content
     * @param  int    $status
     * @param  array  $headers
     *
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function send($content = '', $status = 200, array $headers = [])
    {
        return response($content, $status, $headers);
    }

    /**
     * 发送 JSON 响应
     *
     * @param  string|array $data
     * @param  int          $status
     * @param  array        $headers
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function json($data = [], $status = 200, array $headers = [])
    {
        return response()->json($data, $status, $headers, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 抛出状态码
     *
     * @param  integer $status 状态码
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function throw($status = 200)
    {
        return $this->send('', $status);
    }

    /**
     * 响应认证信息
     *
     * @param  string $token Token
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function auth($token)
    {
        return $this->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => Auth::guard()->factory()->getTTL() * 60
        ], 200, [
            'Authorization' => sprintf('Bearer %s', $token)
        ]);
    }
}