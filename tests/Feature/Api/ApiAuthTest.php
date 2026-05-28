<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SetupScm;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase, SetupScm;

    public function test_login_returns_token(): void
    {
        $this->setUpScm();
        $admin = $this->createAdminUser();

        $response = $this->postJson('/api/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'user']);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $this->setUpScm();

        $response = $this->postJson('/api/login', [
            'email' => 'none@test.com',
            'password' => 'wrong',
        ]);

        $response->assertStatus(422);
    }

    public function test_unauthenticated_cannot_access_protected_routes(): void
    {
        $response = $this->getJson('/api/suppliers');

        $response->assertStatus(401);
    }
}
