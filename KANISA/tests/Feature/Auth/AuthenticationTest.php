<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'login' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'login' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_authenticate_using_phone(): void
    {
        $user = User::factory()->create(['phone' => '0788888888']);

        $response = $this->post('/login', [
            'login' => '0788888888',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_authenticate_using_member_id(): void
    {
        $user = User::factory()->create(['member_id' => 'EAGT-TEST-1234']);

        $response = $this->post('/login', [
            'login' => 'EAGT-TEST-1234',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_admin_and_mchungaji_are_linked_to_all_departments_via_seeder(): void
    {
        $this->seed(\Database\Seeders\ChurchSystemSeeder::class);

        $admin = User::where('email', 'admin@philadelphia.com')->first();
        $deptCount = \App\Models\Department::count();

        $this->assertNotNull($admin);
        $this->assertTrue($admin->isAdmin());
        $this->assertEquals($deptCount, $admin->departments()->count());
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
