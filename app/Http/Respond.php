<?php

namespace App\Http;

use Symfony\Component\HttpFoundation\Response;

class Respond
{
    /**
     * 发送响应
     *
     * @param  string $content
     * @param  int    $status
     * @param  array  $headers
     *
     * @return Response
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
     * @return Response
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
     * @return Response
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
     * @return Response
     */
    public function auth($token)
    {
        return $this->json(compact('token'), 200, [
            'Authorization' => sprintf('Bearer %s', $token)
        ]);
    }
}