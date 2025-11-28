<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    private array $userData;

    public function setUp(): void
    {
        parent::setUp();

        $this->userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];
    }

    public function test_register_success(): void
    {
        $response = $this->postJson('/api/auth/register', $this->userData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Success',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ],
                    'token',
                ],
                'errors',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $this->userData['email'],
            'name' => $this->userData['name'],
        ]);
    }

    public function test_register_duplicate_email_fails(): void
    {
        $this->createUser($this->userData);

        $response = $this->postJson('/api/auth/register', $this->userData);

        $this->assertTrue(
            in_array($response->status(), [409, 422]),
            "Unexpected status code: {$response->status()}"
        );

        $response->assertJson([
            'status' => false,
        ]);
    }

    public function test_register_validation_fails(): void
    {
        $invalidData = [
            'name' => '',
            'email' => 'not-an-email',
            'password' => '123',
        ];

        $response = $this->postJson('/api/auth/register', $invalidData);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'errors',
                'data',
            ]);
    }

    private function createUser(array $data): User
    {
        unset($data['password_confirmation']);
        $data['password'] = Hash::make($data['password']);
        return User::factory()->create($data);
    }
}
