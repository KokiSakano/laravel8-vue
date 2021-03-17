<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
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
     * アクセス可能テスト
     */
    public function testAccess(): void
    {
        $this->get('/')->assertStatus(200);
    }

    /**
     * 登録テスト
     */
    public function testRegister(): void
    {
        $response = $this->post('/register', [
            'name' => $this->user['name'],
            'email' => $this->user['email'],
            'password' => $this->user['password'],
            'password_confirmation' => $this->user['password'],
        ]);
        $response->assertStatus(302)
            ->assertRedirect(route('home'));
    }

    /**
     * ログイン認証テスト
     */
    public function testLogin(): void
    {
        // ユーザー登録
        User::factory()->create([
            'email' => $this->user['email'],
            'password' => \Hash::make($this->user['password']),
        ]);

        $response = $this->post('/login', [
            'email' => $this->user['email'],
            'password' => $this->user['password'],
        ]);

        $response->assertStatus(302)
            ->assertRedirect(route('home'));

        $this->assertAuthenticated();
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
        $response->assertStatus(302)
            ->assertRedirect("/");
        $this->assertGuest();
    }
}
