<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Whisper;

class WhisperTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */

    /**
     * 投稿テスト
     */
    public function testWhisperPost()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/api/whispers/', [
            'whisper' => 'test'
        ]);
        $response -> assertStatus(200);
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
        $user = User::factory()->create();
        $whisper = Whisper::factory()->create();
        $this->actingAs($user);

        $route = '/api/whispers/' . strval($whisper->id);
        $response = $this->delete($route);
        $response -> assertStatus(200);
        $this->assertEquals(0, Whisper::count());
    }

    /**
     * 取得テスト
     */
    public function testWhisperGet()
    {
        $user = User::factory()->create();
        $whisper = Whisper::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/api/whispers/');
        $response -> assertStatus(200);
    }
}
