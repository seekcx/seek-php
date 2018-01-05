<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Response;

class ServerController extends Controller
{
    /**
     * 服务首页
     *
     * @return string
     */
    public function index()
    {
        return respond()->send(sprintf(
            'Seek Api Service, %s.',
            Carbon::now()->format(
                config('app.datetime.format', 'l jS \\of F Y h:i:s.u T')
            )
        ));
    }

    /**
     * 服务器心跳
     *
     * @return string
     */
    public function ping()
    {
        return respond()->send('pong!');
    }

    /**
     * 服务器时间
     *
     * @return Response
     */
    public function time()
    {
        return respond()->send(
            Carbon::now()->format(
                config('app.datetime.format', 'l jS \\of F Y h:i:s.u T')
            )
        );
    }
}
