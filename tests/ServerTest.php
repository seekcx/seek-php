<?php

class ServerTest extends TestCase
{
    /**
     * 服务首页测试
     *
     * @return void
     */
    public function testIndex()
    {
        $response = $this->call('GET', '/');

        $this->assertStringStartsWith('Seek Api Service', $response->content());
    }

    /**
     * Ping 测试
     *
     * @return void
     */
    public function testPing()
    {
        $response = $this->call('GET', '/ping');

        $this->assertEquals('pong!', $response->content());
    }

    /**
     * 服务器时间测试
     *
     * @return void
     */
    public function testTime()
    {
        $response = $this->call('GET', '/time');

        $this->assertStringMatchesFormat('%s %s of %s %d %d:%d:%d.%d UTC', $response->content());
    }
}
