<?php

namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Models\User;

use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected $user = [
        'name' => 'tester',
        'email' => 'test@example.com',
        'password' => "password",
    ];

    /**
     * 登録テスト
     */
    public function testRegister():void
    {
        $this->post('/register', [
            'name' => $this->user['name'],
            'email' => $this->user['email'],
            'password' => $this->user['password'],
            'password_confirmation' => $this->user['password'],
        ]);
        $response = $this->get('/');
        $response
            ->assertStatus(200);
    }

    /**
     * ログイン認証テスト
     */
    public function testLogin(): void
    {
        $this -> post('/login', [
            'email' => $this->user['email'],
            'password' => $this->user['password'],
        ]);

        $response = $this->get('/');
        $response ->assertStatus(200);
    }

    /**
     * ログアウトテスト
     */
    public function testLogout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->assertAuthenticated();

        $response = $this->post('/logout');
        $response = $this->get('/');
        $response->assertStatus(200);
        $this->assertGuest();
    }
}
