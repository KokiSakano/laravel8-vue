<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Whisper;
use App\Models\Good;

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
     * いいねテスト+
     */
    public function testGoodPlus()
    {
        $whisper = $this->whispers->first();
        $id = $whisper->id;
        $this->post('/api/good/p/' . $id)->assertOK();
        $this->post('/api/good/p/' . $id)->assertStatus(208);
        $this->assertEquals(1, Whisper::find($id)->good);
        $good = Good::all()->first();
        $this->assertEquals($this->authUser->id, $good->user_id);
        $this->assertEquals($id, $good->whisper_id);
    }
    /**
     * いいねテスト-
     */
    public function testGoodMinus()
    {
        $whisper = $this->whispers->first();
        $id = $whisper->id;
        $this->post('/api/good/p/' . $id)->assertOK();
        $this->assertEquals(1, Whisper::find($id)->good);
        $this->post('/api/good/m/' . $id)->assertOK();
        $this->post('/api/good/m/' . $id)->assertStatus(208);
        $this->assertEquals(0, Whisper::find($id)->good);
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
        $this->post('/api/good/p/' . $id)->assertOK();
        $this->assertEquals(1, Whisper::find($id)->good);
        $this->get('/api/good/')->assertOK();
    }
}
