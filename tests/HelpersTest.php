<?php

namespace Tests;

use App\Http\Respond;
use Vinkla\Hashids\Facades\Hashids;

class HelpersTest extends \TestCase
{
    public function testRespond()
    {
        $instance = respond();

        $this->assertInstanceOf(Respond::class, $instance);
    }

    public function testHashidsEncode()
    {
        $id = rand(100, 99999);

        $this->assertEquals(Hashids::encode($id), hashids_encode($id));
    }

    public function testHashidsDecode()
    {
        $id = rand(100, 99999);
        $hashid = Hashids::encode($id);

        $this->assertEquals($id, hashids_decode($hashid));
    }

    /**
     * @expectedException \Hashids\HashidsException
     */
    public function testHashidsDecodeException()
    {
        hashids_decode('test');
    }

    public function testHideMobile()
    {
        $mobile = '13812345678';
        $this->assertEquals('138****5678', hide_mobile($mobile));
    }

    public function testHideEmail()
    {
        $email = 'test@test.com';

        $this->assertEquals('te****@test.com', hide_email($email));

        $email = 'test';

        $this->assertEquals('test', hide_email($email));
    }

    /**
     *
     * @dataProvider isEmailProvider
     */
    public function testIsEmail($email, $expected)
    {
        $this->assertEquals($expected, is_email($email));
    }

    public function isEmailProvider()
    {
        return [
            ['test@test.com', true],
            ['test-123@test.com', true],
            ['test_123@test.com', true],
            ['test@123@test.com', false],
            ['test.123@test.com', true],
            ['@test.com', false],
            ['test@', false],
        ];
    }
}