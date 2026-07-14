<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Organisation;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    /**
     * Test user can register
     */
    public function test_user_can_register(): void
    {
        $organisation = Organisation::factory()->create();

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'SecurePass123!@',
            'password_confirmation' => 'SecurePass123!@',
            'org_id' => $organisation->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
    }

    /**
     * Test user cannot register with weak password
     */
    public function test_user_cannot_register_with_weak_password(): void
    {
        $organisation = Organisation::factory()->create();

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'weak',
            'password_confirmation' => 'weak',
            'org_id' => $organisation->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }

    /**
     * Test user can login
     */
    public function test_user_can_login(): void
    {
        $organisation = Organisation::factory()->create();
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'org_id' => $organisation->id,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
            ]);
    }

    /**
     * Test user cannot login with wrong password
     */
    public function test_user_cannot_login_with_wrong_password(): void
    {
        $organisation = Organisation::factory()->create();
        User::factory()->create([
            'email' => 'test@example.com',
            'org_id' => $organisation->id,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrong_password',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test user can get profile when authenticated
     */
    public function test_user_can_get_profile(): void
    {
        $organisation = Organisation::factory()->create();
        $user = User::factory()->create([
            'org_id' => $organisation->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'email' => $user->email,
                    'name' => $user->name,
                ],
            ]);
    }

    /**
     * Test unauthenticated user cannot get profile
     */
    public function test_unauthenticated_user_cannot_get_profile(): void
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401);
    }

    /**
     * Test user can logout
     */
    public function test_user_can_logout(): void
    {
        $organisation = Organisation::factory()->create();
        $user = User::factory()->create([
            'org_id' => $organisation->id,
        ]);

        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/v1/auth/logout');

        $response->assertStatus(200);
    }

    /**
     * Test user can refresh token
     */
    public function test_user_can_refresh_token(): void
    {
        $organisation = Organisation::factory()->create();
        $user = User::factory()->create([
            'org_id' => $organisation->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/refresh', [
                'refresh_token' => 'dummy_token'
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
            ]);
    }
}
