<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */

    /**
     * 編集テスト
     */
    public function testUserEdit()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $route = '/api/users/' . strval($user->id);
        $response = $this->put($route, [
            'name' => 'test',
            'email' => 'test@test.com'
        ]);
        $response -> assertStatus(200);

        $user = User::find($user->id);
        $this->assertEquals($user->name, 'test');
        $this->assertEquals($user->email, 'test@test.com');
    }

    /**
     * 削除テスト
     */
    public function testUserDelete()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $route = '/api/users/' . strval($user->id);
        $response = $this->delete($route);
        $response -> assertStatus(200);
        $this->assertEquals(0, User::count());
    }

    /**
     * 取得テスト
     */
    public function testUserGet()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $route = '/api/users/' . strval($user->id);
        $response = $this->get($route);
        $response -> assertOK();
    }
}
