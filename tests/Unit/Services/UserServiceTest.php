<?php

namespace Tests\Unit\Services;

use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;
use App\Models\User;

class UserServiceTest extends TestCase
{
    protected $userRepository;
    protected $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = Mockery::mock(UserRepository::class);
        $this->userService = new UserService($this->userRepository);
    }

    public function test_get_all_users()
    {
        $this->userRepository->shouldReceive('getAll')->once()->andReturn(collect([]));

        $result = $this->userService->getAll();

        $this->assertIsIterable($result);
    }

    public function test_get_user_by_id()
    {
        $user = User::factory()->make(['id' => 1]);

        $this->userRepository->shouldReceive('findById')->with(1)->once()->andReturn($user);

        $result = $this->userService->getById(1);

        $this->assertInstanceOf(User::class, $result);
    }

    public function test_create_user()
    {
        $data = ['name' => 'John Doe', 'email' => 'john@example.com', 'password' => 'secret'];
        $hashedPassword = Hash::make($data['password']);

        $this->userRepository->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($arg) use ($hashedPassword) {
                return Hash::check('secret', $arg['password']);
            }))
            ->andReturn(new User());

        $result = $this->userService->createUser($data);

        $this->assertInstanceOf(User::class, $result);
    }

    public function test_update_user()
    {
        $this->userRepository->shouldReceive('update')->with(1, ['name' => 'Updated Name'])->once()->andReturn(true);

        $result = $this->userService->updateUser(1, ['name' => 'Updated Name']);

        $this->assertTrue($result);
    }

    public function test_delete_user()
    {
        $this->userRepository->shouldReceive('delete')->with(1)->once();

        $this->userService->deleteUser(1);

        $this->assertTrue(true);
    }
}
