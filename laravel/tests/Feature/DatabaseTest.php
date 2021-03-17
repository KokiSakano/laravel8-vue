<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Whisper;

class DatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function testUserDatabase()
    {
        $testUser = new User();
        $newdata = [
            'name' => 'tester',
            'email' => 'test@test.com',
            'password' => 'testtest',
        ];
        $saveUser = $testUser -> fill($newdata) -> save();
        $this->assertTrue($saveUser);

        $this->assertDatabaseHas('users', [
            'name' => $testUser->name,
            'email' => $testUser->email,
        ]);
    }
    public function testWhisperDatabase()
    {
        $testWhisper = new Whisper();
        $newdata = [
            'whisp' => 'test',
            'user_id' => 1,
            'good' => 0,
        ];
        $saveWhisper = $testWhisper -> fill($newdata) -> save();
        $this->assertTrue($saveWhisper);
    }
}