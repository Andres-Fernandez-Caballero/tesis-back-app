<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserManagmentApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_register_user()
    {
        $response = $this->postJson(route('auth.register.client'), [
            'name' => 'Test User',
            'last_name' => 'Example',
            'phone' => '1122334455',
            'birth_date' => '1990-01-01',
            'gender' => 'other',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_register_user_notifica_credenciales_en_texto_plano_a_slack()
    {
        config(['services.slack.accounts_webhook_url' => 'https://hooks.slack.com/services/test']);
        Http::fake();

        $this->postJson(route('auth.register.client'), [
            'name' => 'Test User',
            'last_name' => 'Example',
            'phone' => '1122334455',
            'birth_date' => '1990-01-01',
            'gender' => 'other',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertStatus(201);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://hooks.slack.com/services/test'
                && str_contains($request['text'], 'cliente')
                && str_contains($request['text'], 'test@example.com')
                && str_contains($request['text'], 'password123');
        });
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
        $response = $this->getJson(route('users.me'));

        $response->assertStatus(401);
    }

    public function test_access_protected_route_with_token()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson(route('users.me'));

        $response->assertStatus(200);
    }

    public function test_update_profile()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->putJson(route('users.update'), [
            'name' => 'Updated Name',
            'email' => $user->email,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);
    }
}
