<?php

namespace Tests\Feature\Controllers\Api;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $admin;
    protected $user;
    protected $users;

    protected function setUp(): void
    {
        parent::setUp();

        $this->users = factory(User::class, 98)->create();
        $this->user = factory(User::class)->create();
        $this->admin = factory(User::class)->state('administrator')
                                           ->create();
    }

    /** @test */
    public function unauthorized_users_can_not_index(): void
    {
        $this->json('GET', route('api.users.index'))
             ->assertUnauthorized();
    }

    /** @test */
    public function non_admin_users_can_not_index(): void
    {
        Passport::actingAs($this->user, ['*']);

        $this->json('GET', route('api.users.index'))
             ->assertForbidden();
    }

    /** @test */
    public function admin_users_can_index(): void
    {
        Passport::actingAs($this->admin, ['*']);

        $this->json('GET', route('api.users.index'))
             ->assertOk()
             ->assertJsonStructure(
                 [
                     'data'  => [
                         ['id', 'name', 'email', 'role', 'created_at', 'updated_at']
                     ],
                     'links' => ['first', 'last', 'prev', 'next', 'self'],
                     'meta'  => [
                         'current_page', 'from', 'last_page', 'path', 'per_page', 'to', 'total',
                         'sort' => ['by', 'direction']
                     ]
                 ]
             );

        $this->json('GET', route('api.users.index', ['sort' => 'name|desc', 'per_page' => 50, 'page' => 2]))
             ->assertOk()
             ->assertJson(
                 [
                     'meta' => ['current_page' => 2,
                                'from'         => 51,
                                'to'           => 100,
                                'per_page'     => 50,
                                'total'        => 100,
                                'last_page'    => 2,]
                 ]
             );

        $randomUser = $this->users->random();
        $this->json('GET', route('api.users.index', ['search' => $randomUser->email]))
             ->assertOk()
             ->assertJson(['data' => [
                 $randomUser->only('id', 'name', 'email', 'role')
             ]]);

        $randomUser = $this->users->random();
        $this->json('GET', route('api.users.index', ['email' => $randomUser->email]))
             ->assertOk()
             ->assertJson(['data' => [
                 $randomUser->only('id', 'name', 'email', 'role')
             ]]);

        $randomUser = $this->users->random();
        $this->json('GET', route('api.users.index', ['name' => $randomUser->name]))
             ->assertOk()
             ->assertJson(['data' => [
                 $randomUser->only('id', 'name', 'email', 'role')
             ]]);
    }

    /** @test */
    public function unauthorized_users_can_not_show(): void
    {
        $this->json('GET', route('api.users.show', $this->users->random()))
             ->assertUnauthorized();
    }

    /** @test */
    public function non_admin_users_can_only_show_themselves(): void
    {
        Passport::actingAs($this->user, ['*']);

        $this->json('GET', route('api.users.show', $this->user))
             ->assertOk()
             ->assertJsonStructure(['data' => ['id', 'name', 'email', 'role', 'created_at', 'updated_at']])
             ->assertJson(['data' => $this->user->only('id', 'name', 'email', 'role')]);

        do {
            $randomUser = $this->users->random();
        } while ($randomUser === $this->user);

        $this->json('GET', route('api.users.show', $randomUser))
             ->assertForbidden();
    }

    /** @test */
    public function admin_users_users_can_show_anybody(): void
    {
        Passport::actingAs($this->admin, ['*']);

        do {
            $randomUser = $this->users->random();
        } while ($randomUser === $this->admin);

        $this->json('GET', route('api.users.show', $randomUser))
             ->assertOk()
             ->assertJsonStructure(['data' => ['id', 'name', 'email', 'role', 'created_at', 'updated_at']])
             ->assertJson(['data' => $randomUser->only('id', 'name', 'email', 'role')]);
    }

    /** @test */
    public function unauthorized_users_can_not_get_me(): void
    {
        $this->json('GET', route('api.users.me'))
             ->assertUnauthorized();
    }

    /** @test */
    public function authorized_users_can_get_me(): void
    {
        Passport::actingAs($this->user, ['*']);

        $this->json('GET', route('api.users.me'))
             ->assertOk()
             ->assertJsonStructure(['data' => ['id', 'name', 'email', 'role', 'created_at', 'updated_at']])
             ->assertJson(['data' => $this->user->only('id', 'name', 'email', 'role')]);
    }

    /** @test */
    public function unauthorized_users_can_not_store(): void
    {
        $user = factory(User::class)->make()
                                    ->only('id', 'name', 'email', 'role');
        $user['password'] = $this->faker->password(8, 64);
        $user['password_confirmation'] = $user['password'];

        $this->json('POST', route('api.users.store'), $user)
             ->assertUnauthorized();

        $this->assertDatabaseMissing('users', Arr::except($user, ['password', 'password_confirmation']));
    }

    /** @test */
    public function non_admin_users_can_not_store(): void
    {
        Passport::actingAs($this->user, ['*']);

        $user = factory(User::class)->make()
                                    ->only('id', 'name', 'email', 'role');
        $user['password'] = $this->faker->password(8, 64);
        $user['password_confirmation'] = $user['password'];

        $this->json('POST', route('api.users.store'), $user)
             ->assertForbidden();

        $this->assertDatabaseMissing('users', Arr::except($user, ['password', 'password_confirmation']));
    }

    /** @test */
    public function admin_users_can_store(): void
    {
        Passport::actingAs($this->admin, ['*']);

        $user = factory(User::class)->make()
                                    ->only('name', 'email', 'role');
        $user['password'] = $this->faker->password(8, 64);
        $user['password_confirmation'] = $user['password'];

        $response = $this->json('POST', route('api.users.store'), $user)
                         ->assertStatus(Response::HTTP_CREATED)
                         ->assertJsonStructure(['data' => ['id', 'name', 'email', 'role', 'created_at', 'updated_at']])
                         ->assertJson(['data' => Arr::except($user, ['password', 'password_confirmation'])])
                         ->decodeResponseJson('data');

        $this->assertDatabaseHas('users', Arr::except($user, ['password', 'password_confirmation']));

        $this->assertTrue(Hash::check($user['password'], User::find($response['id'])->password));
    }

    /** @test */
    public function unauthorized_users_can_not_update(): void
    {
        $user = $this->users->random();
        $user->name = $this->faker->name;
        $user->email = $this->faker->unique()->safeEmail;
        $user->role = $this->faker->randomElement(['administrator', 'user']);

        $data = $user->only('name', 'email', 'role');
        $data['password'] = $this->faker->password(8, 64);
        $data['password_confirmation'] = $data['password'];

        $this->json('PATCH', route('api.users.update', $user), $data)
             ->assertUnauthorized();

        $this->assertDatabaseMissing('users', $user->only('id', 'name', 'email', 'role'));
    }

    /** @test */
    public function non_admin_users_can_only_update_themselves(): void
    {
        Passport::actingAs($this->user, ['*']);

        do {
            $user = $this->users->random();
        } while ($user->isAdministrator());

        $user->name = $this->faker->name;
        $user->email = $this->faker->unique()->safeEmail;
        $user->role = $this->faker->randomElement(['administrator', 'user']);

        $data = $user->only('name', 'email', 'role');
        $data['password'] = $this->faker->password(8, 64);
        $data['password_confirmation'] = $data['password'];

        $this->json('PATCH', route('api.users.update', $user), $data)
             ->assertForbidden();

        $this->assertDatabaseMissing('users', $user->only('id', 'name', 'email', 'role'));

        $user = $user->refresh();

        $this->assertFalse(Hash::check($data['password'], $user->password));

        $this->user->name = $this->faker->name;
        $this->user->email = $this->faker->unique()->safeEmail;

        $data = $this->user->only('name', 'email', 'role');
        $data['role'] = 'administrator';
        $data['password'] = $this->faker->password(8, 64);
        $data['password_confirmation'] = $data['password'];

        $this->json('PATCH', route('api.users.update', $this->user), $data)
             ->assertOk()
             ->assertJson(['message' => 'OK']);

        $expectedRowData = $this->user->only('id', 'name', 'email');
        $expectedRowData['role'] = 'user';
        $this->assertDatabaseHas('users', $expectedRowData);


        $this->assertTrue(Hash::check($data['password'], $this->user->refresh()->password));
    }

    /** @test */
    public function admin_users_can_update_anyone(): void
    {
        Passport::actingAs($this->admin, ['*']);

        $user = $this->users->random();
        $user->name = $this->faker->name;
        $user->email = $this->faker->unique()->safeEmail;
        $user->role = $this->faker->randomElement(['administrator', 'user']);

        $data = $user->only('name', 'email', 'role');
        $data['password'] = $this->faker->password(8, 64);
        $data['password_confirmation'] = $data['password'];

        $this->json('PATCH', route('api.users.update', $user), $data)
             ->assertOk()
             ->assertJson(['message' => 'OK']);

        $this->assertDatabaseHas('users', $user->only('id', 'name', 'email', 'role'));

        $user = $user->refresh();

        $this->assertTrue(Hash::check($data['password'], $user->password));
    }

    /** @test */
    public function unauthorized_users_can_not_destroy(): void
    {
        $user = $this->users->random();
        $this->json('DELETE', route('api.users.destroy', $user))
             ->assertUnauthorized();

        $this->assertDatabaseHas('users', $user->only('id', 'name', 'email', 'role'));
    }

    /** @test */
    public function non_admin_users_can_not_destroy(): void
    {
        Passport::actingAs($this->user, ['*']);

        $user = $this->users->random();
        $this->json('DELETE', route('api.users.destroy', $user))
             ->assertForbidden();

        $this->assertDatabaseHas('users', $user->only('id', 'name', 'email', 'role'));
    }

    /** @test */
    public function admin_users_can_destroy(): void
    {
        Passport::actingAs($this->admin, ['*']);

        $user = $this->users->random();
        $this->json('DELETE', route('api.users.destroy', $user))
             ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('users', $user->only('id', 'name', 'email', 'role'));
    }

    /** @test */
    public function unauthorized_users_can_not_bulk_destroy(): void
    {
        $userIds = $this->users->random($this->faker->numberBetween(10, 20))
                               ->map(function (User $user) {
                                   return $user->id;
                               })
                               ->toArray();

        $this->json('DELETE', route('api.users.destroy.bulk'), ['ids' => $userIds])
             ->assertUnauthorized();

        foreach ($userIds as $userId) {
            $this->assertDatabaseHas('users', ['id' => $userId]);
        }
    }

    /** @test */
    public function non_admin_users_can_not_bulk_destroy(): void
    {
        Passport::actingAs($this->user, ['*']);

        $userIds = $this->users->random($this->faker->numberBetween(10, 20))
                               ->map(function (User $user) {
                                   return $user->id;
                               })
                               ->toArray();

        $this->json('DELETE', route('api.users.destroy.bulk'), ['ids' => $userIds])
             ->assertForbidden();

        foreach ($userIds as $userId) {
            $this->assertDatabaseHas('users', ['id' => $userId]);
        }
    }

    /** @test */
    public function admin_users_can_bulk_destroy(): void
    {
        Passport::actingAs($this->admin, ['*']);

        $userIds = $this->users->random($this->faker->numberBetween(10, 20))
                               ->map(function (User $user) {
                                   return $user->id;
                               })
                               ->toArray();

        $this->json('DELETE', route('api.users.destroy.bulk'), ['ids' => $userIds])
             ->assertStatus(Response::HTTP_NO_CONTENT);


        foreach ($userIds as $userId) {
            $this->assertDatabaseMissing('users', ['id' => $userId]);
        }
    }
}
