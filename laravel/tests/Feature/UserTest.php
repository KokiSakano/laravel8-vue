<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
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
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }
    /**
     * 編集テスト
     */
    public function testUserEdit()
    {
        $route = '/api/users/' . strval($this->user->id);
        $this->put($route, [
            'name' => 'test',
            'email' => 'test@test.com'
        ])->assertStatus(200);

        $user = User::find($this->user->id);
        $this->assertEquals($user->name, 'test');
        $this->assertEquals($user->email, 'test@test.com');
    }

    /**
     * 削除テスト
     */
    public function testUserDelete()
    {
        $route = '/api/users/' . strval($this->user->id);
        $this->delete($route)->assertStatus(200);
        $this->assertEquals(0, User::count());
    }

    /**
     * 取得テスト
     */
    public function testUserGet()
    {
        $route = '/api/users/' . strval($this->user->id);
        $this->get($route)->assertOK();
    }
}
