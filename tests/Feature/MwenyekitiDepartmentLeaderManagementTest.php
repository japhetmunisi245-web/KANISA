<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MwenyekitiDepartmentLeaderManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Department $department;
    protected User $mwenyekiti;
    protected User $katibu;
    protected User $mshirika;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'mchungaji']);
        Role::firstOrCreate(['name' => 'kiongozi']);
        Role::firstOrCreate(['name' => 'mshiriki']);

        $this->department = Department::create([
            'name_sw'        => 'Idara ya Vijana',
            'name_en'        => 'Youth Department',
            'description_sw' => 'Idara ya vijana kanisani.',
            'description_en' => 'Youth department in church.',
            'icon'           => 'fa-users',
            'color'          => '#10b981',
        ]);

        // Mwenyekiti (Chairperson) of Youth Dept
        $this->mwenyekiti = User::create([
            'name'                    => 'Mwenyekiti James',
            'email'                   => 'mwenyekiti@kanisa.com',
            'phone'                   => '0711111111',
            'role'                    => 'kiongozi',
            'cheo'                    => 'Mwenyekiti',
            'password'                => Hash::make('password123'),
            'onboarding_completed_at' => now(),
        ]);
        $this->mwenyekiti->assignRole('kiongozi');
        $this->mwenyekiti->departments()->attach($this->department->id, [
            'role' => 'kiongozi',
            'cheo' => 'Mwenyekiti',
        ]);

        // Katibu (Secretary - a leader, but not Mwenyekiti)
        $this->katibu = User::create([
            'name'                    => 'Katibu Mary',
            'email'                   => 'katibu@kanisa.com',
            'phone'                   => '0722222222',
            'role'                    => 'kiongozi',
            'cheo'                    => 'Katibu',
            'password'                => Hash::make('password123'),
            'onboarding_completed_at' => now(),
        ]);
        $this->katibu->assignRole('kiongozi');
        $this->katibu->departments()->attach($this->department->id, [
            'role' => 'kiongozi',
            'cheo' => 'Katibu',
        ]);

        // Mshirika (Regular member)
        $this->mshirika = User::create([
            'name'                    => 'Mshirika Peter',
            'email'                   => 'mshirika@kanisa.com',
            'phone'                   => '0733333333',
            'role'                    => 'mshirika',
            'password'                => Hash::make('password123'),
            'onboarding_completed_at' => now(),
        ]);
        $this->mshirika->assignRole('mshiriki');
        $this->mshirika->departments()->attach($this->department->id, [
            'role' => 'mshirika',
            'cheo' => null,
        ]);
    }

    public function test_mwenyekiti_can_register_department_leader_with_password(): void
    {
        $response = $this->actingAs($this->mwenyekiti)->post(route('department-leaders.store'), [
            'department_id'         => $this->department->id,
            'name'                  => 'Mhasibu Baraka',
            'email'                 => 'mhasibu@kanisa.com',
            'phone'                 => '0744444444',
            'gender'                => 'male',
            'cheo'                  => 'Mhasibu',
            'password'              => 'Baraka2026!',
            'password_confirmation' => 'Baraka2026!',
        ]);

        $response->assertSessionHas('success');
        $response->assertSessionHas('registered_leader_credentials');

        $newLeader = User::where('email', 'mhasibu@kanisa.com')->first();
        $this->assertNotNull($newLeader);
        $this->assertEquals('Mhasibu Baraka', $newLeader->name);
        $this->assertEquals('kiongozi', $newLeader->role);
        $this->assertEquals('Mhasibu', $newLeader->cheo);
        $this->assertTrue($newLeader->hasRole('kiongozi'));
        $this->assertNotNull($newLeader->onboarding_completed_at);
        $this->assertTrue(Hash::check('Baraka2026!', $newLeader->password));

        // Check department_user pivot
        $this->assertTrue(
            $this->department->users()
                ->where('users.id', $newLeader->id)
                ->wherePivot('role', 'kiongozi')
                ->wherePivot('cheo', 'Mhasibu')
                ->exists()
        );

        // Verify newly registered leader can authenticate
        auth()->logout();
        $loginResponse = $this->post('/login', [
            'login'    => 'mhasibu@kanisa.com',
            'password' => 'Baraka2026!',
        ]);
        $this->assertAuthenticatedAs($newLeader);
    }

    public function test_mwenyekiti_can_update_department_leader_password(): void
    {
        $response = $this->actingAs($this->mwenyekiti)->post(route('department-leaders.password', $this->katibu->id), [
            'department_id'         => $this->department->id,
            'password'              => 'NewPassword2026!',
            'password_confirmation' => 'NewPassword2026!',
        ]);

        $response->assertSessionHas('success');
        $response->assertSessionHas('updated_leader_credentials');

        $this->katibu->refresh();
        $this->assertTrue(Hash::check('NewPassword2026!', $this->katibu->password));

        // Verify Katibu can log in with newly assigned password
        auth()->logout();
        $this->post('/login', [
            'login'    => 'katibu@kanisa.com',
            'password' => 'NewPassword2026!',
        ]);
        $this->assertAuthenticatedAs($this->katibu);
    }

    public function test_non_mwenyekiti_cannot_register_department_leader(): void
    {
        // Katibu tries to register another leader
        $response = $this->actingAs($this->katibu)->post(route('department-leaders.store'), [
            'department_id'         => $this->department->id,
            'name'                  => 'Hacker Leader',
            'email'                 => 'hacker@kanisa.com',
            'phone'                 => '0755555555',
            'cheo'                  => 'Makamu Mwenyekiti',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('users', ['email' => 'hacker@kanisa.com']);
    }

    public function test_regular_member_cannot_register_department_leader(): void
    {
        // Mshirika tries to register a leader
        $response = $this->actingAs($this->mshirika)->post(route('department-leaders.store'), [
            'department_id'         => $this->department->id,
            'name'                  => 'Fake Leader',
            'email'                 => 'fake@kanisa.com',
            'phone'                 => '0766666666',
            'cheo'                  => 'Mhasibu',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('users', ['email' => 'fake@kanisa.com']);
    }

    public function test_duplicate_cheo_in_same_department_is_rejected(): void
    {
        // Katibu already exists in Youth Dept ($this->katibu)
        $response = $this->actingAs($this->mwenyekiti)->post(route('department-leaders.store'), [
            'department_id'         => $this->department->id,
            'name'                  => 'Another Katibu',
            'email'                 => 'katibu2@kanisa.com',
            'phone'                 => '0777777777',
            'cheo'                  => 'Katibu',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('users', ['email' => 'katibu2@kanisa.com']);
    }

    public function test_mwenyekiti_can_remove_leader_from_department(): void
    {
        $response = $this->actingAs($this->mwenyekiti)->post(route('department-leaders.remove', $this->katibu->id), [
            'department_id' => $this->department->id,
        ]);

        $response->assertSessionHas('success');

        $this->assertFalse(
            $this->department->users()->where('users.id', $this->katibu->id)->exists()
        );
    }

    public function test_regular_member_cannot_self_assign_kiongozi_in_onboarding(): void
    {
        $newMember = User::create([
            'name'     => 'Ordinary John',
            'email'    => 'ordinary@kanisa.com',
            'phone'    => '0788888888',
            'role'     => 'mshirika',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($newMember)->post(route('onboarding.complete'), [
            'role'          => 'kiongozi',
            'department_id' => $this->department->id,
            'cheo'          => 'Katibu',
        ]);

        $response->assertSessionHas('error');
        $newMember->refresh();
        $this->assertEquals('mshirika', $newMember->role);
    }
}
