<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{

    use RefreshDatabase;
    private $userLogin;


    public function setUp(): void
    {
        parent::setUp();

        $this->userLogin = [
            "email" => "test@test.com",
            "password" => "password"
        ];
    }

    /**
     * A basic feature test example.
     */
    public function test_log_in_fail_not_found_email(): void
    {
        $response = $this->post('/api/auth/login', $this->userLogin);
        $response->assertStatus(422);
    }

    public function test_log_in_fail_wrong_password(): void
    {
        $this->createUser($this->userLogin);
        $this->userLogin["password"] = "wrong_password";
        $response = $this->post('/api/auth/login', $this->userLogin);
        $response->assertStatus(401);
    }

    public function test_log_in_success(): void
    {
        $this->createUser($this->userLogin);
        $response = $this->post('/api/auth/login', $this->userLogin);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'token',
                'user'
            ]
        ]);
    }

    private function createUser($data)
    {
        if (empty($data["email"])) {
            $data["email"] = $this->userLogin["email"];
        }
        if (empty($data["password"])) {
            $data["password"] = $this->userLogin["password"];
        }
        // Hash the password before saving
        $data["password"] = Hash::make($data["password"]);

        User::factory()->create($data);
    }
}
