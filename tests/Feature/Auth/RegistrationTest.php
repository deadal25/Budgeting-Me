<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register_with_valid_registration_code(): void
    {
        $code = \App\Models\AppSetting::getActiveRegistrationCode();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'registration_code' => $code,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_new_users_cannot_register_without_registration_code(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User 2',
            'email' => 'test2@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('registration_code');
        $this->assertGuest();
    }

    public function test_new_users_cannot_register_with_wrong_registration_code(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User 3',
            'email' => 'test3@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'registration_code' => 'ZZZZ',
        ]);

        $response->assertSessionHasErrors('registration_code');
        $this->assertGuest();
    }

    public function test_new_users_can_register_with_lowercase_code(): void
    {
        $code = strtolower(\App\Models\AppSetting::getActiveRegistrationCode());

        $response = $this->post('/register', [
            'name' => 'Test User 4',
            'email' => 'test4@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'registration_code' => $code,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
