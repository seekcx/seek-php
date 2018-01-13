<?php

namespace Tests\User;

use App\Entities\User;
use Tymon\JWTAuth\JWTAuth;
use Laravel\Lumen\Testing\DatabaseTransactions;

/**
 * @group UserShip
 */
class ShipTest extends \TestCase
{
    use DatabaseTransactions;

    protected $firstUser = null;
    protected $firstStat = null;

    protected $secondUser = null;
    protected $secondStat = null;

    public function setUp()
    {
        parent::setUp();

        $this->firstUser = factory(User::class)->create([
            'name'     => 'testship1',
            'password' => 'test123'
        ]);

        $this->firstStat = factory(User\Stat::class)->create([
            'user_id'   => $this->firstUser->id,
            'followers' => rand(100, 999999),
            'following' => rand(100, 999999)
        ]);

        $this->secondUser = factory(User::class)->create([
            'name'     => 'testship2',
            'password' => 'test123'
        ]);

        $this->secondStat = factory(User\Stat::class)->create([
            'user_id'   => $this->secondUser->id,
            'followers' => rand(100, 999999),
            'following' => rand(100, 999999)
        ]);
    }

    /**
     * 测试关注
     *
     */
    public function testFollow()
    {
        $auth = app(JWTAuth::class);

        $firstToken  = $auth->fromUser($this->firstUser);
        $secondToken = $auth->fromUser($this->secondUser);

        $this->post(sprintf('/user/%s/followers', hashids_encode($this->firstUser->id)), [], [
            'Authorization' => 'Bearer ' .$secondToken
        ]);

        $this->seeStatusCode(205);

        $this->seeInDatabase('user_ship', [
            'user_id'     => $this->firstUser->id,
            'follower_id' => $this->secondUser->id
        ]);

        $this->seeInDatabase('user_stat', [
            'user_id'   => $this->firstUser->id,
            'followers' => $this->firstStat->followers + 1
        ]);

        $this->seeInDatabase('user_stat', [
            'user_id'   => $this->secondUser->id,
            'following' => $this->secondStat->following + 1
        ]);

        $this->post(sprintf('/user/%s/followers', hashids_encode($this->secondUser->id)), [], [
            'Authorization' => 'Bearer ' .$firstToken
        ]);

        $this->seeStatusCode(205);

        $this->seeInDatabase('user_ship', [
            'user_id'     => $this->secondUser->id,
            'follower_id' => $this->firstUser->id,
            'cross'       => 1
        ]);

        $this->seeInDatabase('user_stat', [
            'user_id'   => $this->secondUser->id,
            'followers' => $this->secondStat->followers + 1
        ]);

        $this->seeInDatabase('user_stat', [
            'user_id'   => $this->firstUser->id,
            'following' => $this->firstStat->following + 1
        ]);

        // 互相关注结果
        $this->seeInDatabase('user_ship', [
            'user_id'     => $this->firstUser->id,
            'follower_id' => $this->secondUser->id,
            'cross'       => 1
        ]);

        // 已关注
        $this->post(sprintf('/user/%s/followers', hashids_encode($this->firstUser->id)), [], [
            'Authorization' => 'Bearer ' .$secondToken
        ]);

        $this->seeStatusCode(400);
        $this->seeJson([
            'message' => '你已经关注 ta 啦'
        ]);

        // 关注自己
        $this->post(sprintf('/user/%s/followers', hashids_encode($this->firstUser->id)), [], [
            'Authorization' => 'Bearer ' .$firstToken
        ]);

        $this->seeStatusCode(400);
        $this->seeJson([
            'message' => '不能关注自己'
        ]);
    }

    /**
     * 测试取关
     *
     */
    public function testUnfollow()
    {
        $auth = app(JWTAuth::class);

        $firstToken  = $auth->fromUser($this->firstUser);
        $secondToken = $auth->fromUser($this->secondUser);

        // 未关注
        $this->delete(sprintf('/user/%s/followers', hashids_encode($this->firstUser->id)), [], [
            'Authorization' => 'Bearer ' .$secondToken
        ]);

        $this->seeStatusCode(400);
        $this->seeJson([
            'message' => '你没有关注 ta'
        ]);

        // 关注自己
        $this->delete(sprintf('/user/%s/followers', hashids_encode($this->firstUser->id)), [], [
            'Authorization' => 'Bearer ' .$firstToken
        ]);

        $this->seeStatusCode(400);
        $this->seeJson([
            'message' => '不能取关自己'
        ]);

        factory(User\Ship::class)->create([
            'user_id' => $this->firstUser->id,
            'follower_id' => $this->secondUser->id,
            'cross' => 1
        ]);

        factory(User\Ship::class)->create([
            'user_id' => $this->secondUser->id,
            'follower_id' => $this->firstUser->id,
            'cross' => 1
        ]);

        $this->seeInDatabase('user_ship', [
            'user_id'     => $this->firstUser->id,
            'follower_id' => $this->secondUser->id,
            'cross' => 1
        ]);

        $this->seeInDatabase('user_ship', [
            'user_id' => $this->secondUser->id,
            'follower_id' => $this->firstUser->id,
            'cross' => 1
        ]);

        $this->delete(sprintf('/user/%s/followers', hashids_encode($this->firstUser->id)), [], [
            'Authorization' => 'Bearer ' .$secondToken
        ]);

        $this->seeStatusCode(205);

        $this->notSeeInDatabase('user_ship', [
            'user_id'     => $this->firstUser->id,
            'follower_id' => $this->secondUser->id
        ]);

        $this->seeInDatabase('user_ship', [
            'user_id' => $this->secondUser->id,
            'follower_id' => $this->firstUser->id,
            'cross' => 0
        ]);

        $this->seeInDatabase('user_stat', [
            'user_id' => $this->firstUser->id,
            'followers' => $this->firstStat->followers - 1
        ]);

        $this->seeInDatabase('user_stat', [
            'user_id' => $this->secondUser->id,
            'following' => $this->secondStat->following - 1
        ]);

        $this->delete(sprintf('/user/%s/followers', hashids_encode($this->secondUser->id)), [], [
            'Authorization' => 'Bearer ' .$firstToken
        ]);

        $this->seeStatusCode(205);

        $this->notSeeInDatabase('user_ship', [
            'user_id' => $this->secondUser->id,
            'follower_id' => $this->firstUser->id,
        ]);

        $this->seeInDatabase('user_stat', [
            'user_id' => $this->firstUser->id,
            'following' => $this->firstStat->following - 1
        ]);

        $this->seeInDatabase('user_stat', [
            'user_id' => $this->secondUser->id,
            'followers' => $this->secondStat->followers - 1
        ]);
    }
}