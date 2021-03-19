<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Whisper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

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
        ])->assertStatus(200);
        $this->assertEquals(1, Whisper::count());
    }

    /**
     * 編集テスト
     */
    public function testWhisperEdit()
    {
        $user = User::factory()->create();
        $whisper = Whisper::factory()->create();
        $this->actingAs($user);

        $route = '/api/whispers/' . strval($whisper->id);
        $response = $this->put($route, [
            'whisp' => 'test',
        ]);
        $response -> assertStatus(200);

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
        $this->delete($route)->assertStatus(200);
        $this->assertEquals(0, Whisper::count());
    }

    /**
     * 取得テスト
     */
    public function testWhisperGet()
    {
        $whisper = Whisper::factory()->create();

        $this->get('/api/whispers/')->assertStatus(200);
    }
}
