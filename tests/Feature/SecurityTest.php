<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Feature tests untuk keamanan route.
 * 
 * Test class ini memverifikasi bahwa route sensitif
 * tidak dapat diakses tanpa autentikasi.
 */
class SecurityTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Gunakan user yang sudah ada atau buat baru
        $this->user = User::firstOrCreate(
            ['email' => 'admin_security_test@test.com'],
            [
                'nama' => 'Admin Security Test',
                'password' => bcrypt('password'),
                'level' => 'Admin',
            ]
        );
    }

    /**
     * Test: Route settings membutuhkan autentikasi.
     */
    public function test_settings_requires_authentication(): void
    {
        $response = $this->get('/settings');
        
        $response->assertRedirect('/login');
    }

    /**
     * Test: Route laporan membutuhkan autentikasi.
     */
    public function test_laporan_requires_authentication(): void
    {
        $response = $this->get('/laporan');
        
        $response->assertRedirect('/login');
    }

    /**
     * Test: Route tripay config membutuhkan autentikasi.
     */
    public function test_tripay_config_requires_authentication(): void
    {
        $response = $this->get('/tripay/config');
        
        $response->assertRedirect('/login');
    }

    /**
     * Test: Route fonnte membutuhkan autentikasi.
     */
    public function test_fonnte_requires_authentication(): void
    {
        $response = $this->get('/fonnte');
        
        $response->assertRedirect('/login');
    }

    /**
     * Test: Route export tagihan membutuhkan autentikasi.
     */
    public function test_export_tagihan_requires_authentication(): void
    {
        $response = $this->get('/export-tagihan/1/2026');
        
        $response->assertRedirect('/login');
    }

    /**
     * Test: Route pelanggan export membutuhkan autentikasi.
     */
    public function test_pelanggan_export_requires_authentication(): void
    {
        $response = $this->get('/pelanggan/export');
        
        $response->assertRedirect('/login');
    }

    /**
     * Test: User yang login dapat mengakses settings.
     */
    public function test_authenticated_user_can_access_settings(): void
    {
        $response = $this->actingAs($this->user)->get('/settings');
        
        $response->assertStatus(200);
    }

    /**
     * Test: User yang login dapat mengakses laporan.
     */
    public function test_authenticated_user_can_access_laporan(): void
    {
        $response = $this->actingAs($this->user)->get('/laporan');
        
        $response->assertStatus(200);
    }

    /**
     * Test: Dashboard home membutuhkan autentikasi.
     */
    public function test_home_requires_authentication(): void
    {
        $response = $this->get('/home');
        
        $response->assertRedirect('/login');
    }

    /**
     * Test: User yang login dapat mengakses home.
     */
    public function test_authenticated_user_can_access_home(): void
    {
        $response = $this->actingAs($this->user)->get('/home');
        
        $response->assertStatus(200);
    }
}
