<?php

namespace Tests\Unit\Repositories;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new UserRepository();
    }

    public function test_get_all_users()
    {
        User::factory(5)->create();

        $result = $this->userRepository->getAll();

        $this->assertCount(5, $result);
    }

    public function test_find_user_by_id()
    {
        $user = User::factory()->create();

        $foundUser = $this->userRepository->findById($user->id);

        $this->assertEquals($user->id, $foundUser->id);
    }

    public function test_create_user()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password')
        ];

        $user = $this->userRepository->create($data);

        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
        $this->assertInstanceOf(User::class, $user);
    }

    public function test_update_user()
    {
        $user = User::factory()->create();
        $updatedData = ['name' => 'Updated Name'];

        $this->userRepository->update($user->id, $updatedData);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);
    }

    public function test_delete_user()
    {
        $user = User::factory()->create();

        $this->userRepository->delete($user->id);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
