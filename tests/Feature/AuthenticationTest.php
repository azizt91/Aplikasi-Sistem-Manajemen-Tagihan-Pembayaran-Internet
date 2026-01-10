<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Feature tests untuk autentikasi.
 * 
 * Test class ini menguji fitur login dan registrasi.
 */
class AuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test: Halaman login dapat diakses.
     */
    public function test_login_page_can_be_rendered(): void
    {
        $response = $this->get('/login');
        
        $response->assertStatus(200);
    }

    /**
     * Test: Halaman register dapat diakses.
     */
    public function test_register_page_can_be_rendered(): void
    {
        $response = $this->get('/register');
        
        $response->assertStatus(200);
    }

    /**
     * Test: Halaman pelanggan login dapat diakses.
     */
    public function test_pelanggan_login_page_can_be_rendered(): void
    {
        $response = $this->get('/pelanggan-login');
        
        $response->assertStatus(200);
    }

    /**
     * Test: User dapat login dengan kredensial valid.
     */
    public function test_user_can_login_with_valid_credentials(): void
    {
        // Gunakan user yang sudah ada atau buat baru
        $user = User::firstOrCreate(
            ['email' => 'auth_test@test.com'],
            [
                'nama' => 'Auth Test',
                'password' => bcrypt('password123'),
                'level' => 'Admin',
            ]
        );

        $response = $this->post('/login', [
            'email' => 'auth_test@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test: User tidak dapat login dengan kredensial invalid.
     */
    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'nonexistent@test.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    /**
     * Test: User dapat logout.
     */
    public function test_user_can_logout(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'logout_test@test.com'],
            [
                'nama' => 'Logout Test',
                'password' => bcrypt('password123'),
                'level' => 'Admin',
            ]
        );

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
    }
}
