<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Whisper;
use App\Models\Good;
use App\Models\Reply;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GoodTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function setUp(): void
    {
        parent::setUp();
        User::factory()->count(10)->create();
        $this->authUser = User::factory()->create();
        $this->users = User::all();
        $this->whispers = Whisper::factory()->count(10)->create();
        $this->actingAs($this->authUser);
    }

    /**
     * いいねテスト+whisp
     */
    public function testWhispGoodPlus()
    {
        $whisper = $this->whispers->first();
        $id = $whisper->id;
        $this->post('/api/good/p/', [
            'id' => $id,
            'whispType' => 'whisp1',
        ])->assertOK();
        $this->post('/api/good/p/', [
            'id' => $id,
            'whispType' => 'whisp1',
        ])->assertStatus(208);
        $this->assertEquals(1, Whisper::find($id)->good);
        $good = Good::all()->first();
        $this->assertEquals($this->authUser->id, $good->user_id);
        $this->assertEquals($id, $good->whisper_id);
    }
    /**
     * いいねテスト-whisp
     */
    public function testWhispGoodMinus()
    {
        $whisper = $this->whispers->first();
        $id = $whisper->id;
        $this->post('/api/good/p/', [
            'id' => $id,
            'whispType' => 'whisp1',
        ])->assertOK();
        $this->assertEquals(1, Whisper::find($id)->good);
        $this->post('/api/good/m/', [
            'id' => $id,
            'whispType' => 'whisp1',
        ])->assertOK();
        $this->post('/api/good/m/', [
            'id' => $id,
            'whispType' => 'whisp1',
        ])->assertStatus(208);
        $this->assertEquals(0, Whisper::find($id)->good);
        $good = Good::all()->first();
        $this->assertEquals(null, $good);
    }
    /**
     * いいねテスト+reply
     */
    public function tesReplyGoodPlus()
    {
        $whisper = $this->whispers->first();
        $reply = Reply::create([
            "user_id" => $this->authUser->id,
            "whisper_id" => $whisper->id,
            "whisp" => "test",
            "good" => 0,
        ]);
        //$reply = Reply::where('whisp', "test")->first();
        $id = $reply->id;
        $this->post('/api/good/p/', [
            'id' => $id,
            'whispType' => 'reply',
        ])->assertOK();
        $this->post('/api/good/p/', [
            'id' => $id,
            'whispType' => 'reply',
        ])->assertStatus(208);
        $this->assertEquals(1, Reply::find($id)->good);
        $good = Good::all()->first();
        $this->assertEquals($this->authUser->id, $good->user_id);
        $this->assertEquals($id, $good->whisper_id);
    }
    /**
     * いいねテスト-reply
     */
    public function testReplyGoodMinus()
    {
        $whisper = $this->whispers->first();
        $reply = Reply::create([
            "user_id" => $this->authUser->id,
            "whisper_id" => $whisper->id,
            "whisp" => "test",
            "good" => 0,
        ]);
        //$reply = Reply::where('whisp', "test")->first();
        $id = $reply->id;
        $this->post('/api/good/p/', [
            'id' => $id,
            'whispType' => 'reply',
        ])->assertOK();
        $this->assertEquals(1, Reply::find($id)->good);
        $this->post('/api/good/m/', [
            'id' => $id,
            'whispType' => 'reply',
        ])->assertOK();
        $this->post('/api/good/m/', [
            'id' => $id,
            'whispType' => 'reply',
        ])->assertStatus(208);
        $this->assertEquals(0, Reply::find($id)->good);
        $good = Good::all()->first();
        $this->assertEquals(null, $good);
    }
    /**
     * AuthのGood取得テスト
     */
    public function testGoodGet()
    {
        $whisper = $this->whispers->first();
        $id = $whisper->id;
        $this->post('/api/good/p/', [
            'id' => $id,
            'whispType' => 'whisp1',
        ])->assertOK();
        $this->assertEquals(1, Whisper::find($id)->good);
        $this->get('/api/good/')->assertOK();
    }
}
