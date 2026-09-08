<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    private function admin(string $email = 'staff@example.com'): Admin
    {
        return Admin::create(['name' => 'Staff', 'email' => $email, 'profile_id' => $email, 'password' => 'original-password', 'status' => true]);
    }

    public function test_guests_cannot_view_or_change_password(): void
    {
        $this->get('/admin/settings/change-password')->assertRedirect('/admin/login');
        $this->put('/admin/settings/change-password')->assertRedirect('/admin/login');
    }

    public function test_staff_without_site_or_role_can_view_settings(): void
    {
        $this->withoutVite()->actingAs($this->admin(), 'admin')
            ->get('/admin/settings/change-password')->assertOk()
            ->assertSee('Current password')->assertSee('Confirm new password');
    }

    public function test_password_change_only_updates_authenticated_admin(): void
    {
        $admin = $this->admin();
        $other = $this->admin('other@example.com');
        $this->actingAs($admin, 'admin')->put('/admin/settings/change-password', [
            'current_password' => 'original-password', 'password' => 'replacement-password',
            'password_confirmation' => 'replacement-password', 'admin_id' => $other->id,
        ])->assertRedirect('/admin/settings/change-password')->assertSessionHas('success');
        $this->assertTrue(Hash::check('replacement-password', $admin->fresh()->password));
        $this->assertFalse(Hash::check('original-password', $admin->fresh()->password));
        $this->assertTrue(Hash::check('original-password', $other->fresh()->password));
        $this->assertNotEmpty($admin->fresh()->remember_token);
        $this->assertAuthenticatedAs($admin, 'admin');
    }

    public function test_wrong_current_password_is_rejected(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'admin')->put('/admin/settings/change-password', [
            'current_password' => 'wrong-password', 'password' => 'replacement-password',
            'password_confirmation' => 'replacement-password',
        ])->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('original-password', $admin->fresh()->password));
    }

    public function test_invalid_new_passwords_are_rejected(): void
    {
        $admin = $this->admin();
        foreach ([['short', 'short'], ['replacement-password', 'mismatch'], ['original-password', 'original-password']] as [$password, $confirmation]) {
            $this->actingAs($admin, 'admin')->put('/admin/settings/change-password', [
                'current_password' => 'original-password', 'password' => $password,
                'password_confirmation' => $confirmation,
            ])->assertSessionHasErrors('password');
        }
        $this->assertTrue(Hash::check('original-password', $admin->fresh()->password));
    }
}
