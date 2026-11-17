<?php

namespace Tests\Feature\Auth;

use App\Models\Sekbid;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertOk()
            ->assertSee('name="nip"', false)
            ->assertSee('name="no_hp"', false)
            ->assertSee('name="jabatan"', false)
            ->assertSee('name="sekbid_id"', false);
    }

    public function test_new_users_can_register(): void
    {
        $sekbid = Sekbid::create(['nama' => 'Bidang Test']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'nip' => '123456789012345678',
            'email' => 'test@example.com',
            'no_hp' => '081234567890',
            'jabatan' => 'Staf',
            'sekbid_id' => $sekbid->id,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'nip' => '123456789012345678',
            'role' => 'user',
        ]);
    }
}
