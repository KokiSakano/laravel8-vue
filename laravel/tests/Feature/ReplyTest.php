<?php

namespace Tests\Feature;


use App\Models\User;
use App\Models\Whisper;
use App\Models\Reply;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReplyTest extends TestCase
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
     * 投稿テスト
     */
    public function testReplyPost()
    {
        $this->post('/api/replies/', [
            'reply' => 'reply test',
            'id' => 1,
        ])->assertOK();
        $this->assertEquals(1, Reply::count());
        $this->assertEquals('reply test', Reply::all()->first()->whisp);
    }

    /**
     * 編集テスト
     */
    public function testReplyEdit()
    {
        $whisper = $this->whispers->first();
        $reply = Reply::create([
            "user_id" => $this->authUser->id,
            "whisper_id" => $whisper->id,
            "whisp" => "reply test",
            "good" => 0,
        ]);
        $route = '/api/replies/' . strval($reply->id);
        $response = $this->put($route, [
            'whisp' => 'edit test',
        ]);
        $response->assertOK();

        $reply = Reply::find($reply->id);
        $this->assertEquals($reply->whisp, 'edit test');
    }

    /**
     * 削除テスト
     */
    public function testReplyDelete()
    {
        $whisper = $this->whispers->first();
        $reply = Reply::create([
            "user_id" => $this->authUser->id,
            "whisper_id" => $whisper->id,
            "whisp" => "reply test",
            "good" => 0,
        ]);
        $route = '/api/replies/' . strval($reply->id);
        $this->delete($route)->assertOK();
        $this->assertEquals(0, Reply::count());
    }

    /**
     * 取得テスト
     */
    // Top画面(ログイン済み)での挙動
    public function testReplyGet()
    {
        $whisper = $this->whispers->first();
        $reply = Reply::create([
            "user_id" => $this->authUser->id,
            "whisper_id" => $whisper->id,
            "whisp" => "reply test",
            "good" => 0,
        ]);
        $route = '/api/replies/' . strval($reply->id);
        $this->get($route)->assertOK();
    }
}
