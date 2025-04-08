<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserManagmentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_user()
    {
        $response = $this->postJson(route('auth.register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_login_user()
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $response = $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)->assertJsonStructure(['token']);
    }

    public function test_access_protected_route_without_token()
    {
        $response = $this->getJson(route('user.profile'));

        $response->assertStatus(401);
    }

    public function test_access_protected_route_with_token()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson(route('user.profile'));

        $response->assertStatus(200);
    }

    public function test_update_profile()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->putJson(route('user.edit-profile'), [
            'name' => 'Updated Name'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);
    }
}
