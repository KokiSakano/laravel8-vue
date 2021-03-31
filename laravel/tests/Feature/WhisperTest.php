<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Whisper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

class WhisperTest extends TestCase
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
        $user = User::factory()->create();
        $this->actingAs($user);
    }

    /**
     * 投稿テスト
     */
    public function testWhisperPost()
    {
        $this->post('/api/whispers/', [
            'whisper' => 'test'
        ])->assertOK();
        $this->assertEquals(1, Whisper::count());
        $this->assertEquals('test', Whisper::all()->first()->whisp);
    }

    /**
     * 編集テスト
     */
    public function testWhisperEdit()
    {
        $whisper = Whisper::factory()->create();

        $route = '/api/whispers/' . strval($whisper->id);
        $response = $this->put($route, [
            'whisp' => 'test',
        ]);
        $response->assertOK();

        $whisper = Whisper::find($whisper->id);
        $this->assertEquals($whisper->whisp, 'test');
    }

    /**
     * 削除テスト
     */
    public function testWhisperDelete()
    {
        $whisper = Whisper::factory()->create();

        $route = '/api/whispers/' . strval($whisper->id);
        $this->delete($route)->assertOK();
        $this->assertEquals(0, Whisper::count());
    }

    /**
     * 取得テスト
     */
    // Top画面(ログイン済み)での挙動
    public function testWhisperGet1()
    {
        Whisper::factory()->create();

        $this->get('/api/whispers?page=1/')->assertOK();
    }
    // Top画面(ログインしていない)での挙動
    public function testWhisperGet2()
    {
        Whisper::factory()->create();

        $this->get('/api/whispers/noauth?page=1/')->assertOK();
    }
    // user画面での挙動
    public function testWhisperGet3()
    {
        User::factory()->count(15)->create();
        Whisper::factory()->count(10)->create();
        $id = User::all()[7]->id;
        $this->get('/api/whispers/profile/' . $id . '?page=1/')->assertOK();
    }
}
