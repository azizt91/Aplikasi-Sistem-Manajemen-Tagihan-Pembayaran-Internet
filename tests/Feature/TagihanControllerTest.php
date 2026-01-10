<?php

namespace Tests\Feature;

use App\Models\Bulan;
use App\Models\Paket;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Feature tests untuk TagihanController.
 * 
 * Test class ini menguji endpoint controller tagihan.
 */
class TagihanControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Gunakan user yang sudah ada atau buat baru
        $this->user = User::firstOrCreate(
            ['email' => 'admin_test@test.com'],
            [
                'nama' => 'Admin Test',
                'password' => bcrypt('password'),
                'level' => 'Admin',
            ]
        );
    }

    /**
     * Test: Halaman tagihan membutuhkan autentikasi.
     */
    public function test_tagihan_page_requires_authentication(): void
    {
        $response = $this->get('/tagihan');
        
        $response->assertRedirect('/login');
    }

    /**
     * Test: User dapat mengakses halaman tagihan.
     */
    public function test_authenticated_user_can_access_tagihan(): void
    {
        $response = $this->actingAs($this->user)->get('/tagihan');
        
        $response->assertStatus(200);
    }

    /**
     * Test: User dapat mengakses halaman buka tagihan.
     */
    public function test_authenticated_user_can_access_buka_tagihan(): void
    {
        $response = $this->actingAs($this->user)->get('/tagihan/buka-tagihan');
        
        $response->assertStatus(200);
    }

    /**
     * Test: Dapat mengakses halaman lunas tagihan.
     */
    public function test_authenticated_user_can_access_lunas_tagihan(): void
    {
        $response = $this->actingAs($this->user)->get('/tagihan/lunas-tagihan');
        
        $response->assertStatus(200);
    }

    /**
     * Test: Validasi gagal jika data tidak lengkap.
     */
    public function test_create_tagihan_validation_fails_without_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post('/tagihan/store-tagihan', [
            // Tidak ada data
        ]);

        $response->assertSessionHasErrors(['bulan', 'tahun', 'id_pelanggan']);
    }

    /**
     * Test: Pelanggan lunas dapat diakses.
     */
    public function test_authenticated_user_can_access_pelanggan_lunas(): void
    {
        $response = $this->actingAs($this->user)->get('/pelanggan-lunas');
        
        $response->assertStatus(200);
    }

    /**
     * Test: Pelanggan belum lunas dapat diakses.
     */
    public function test_authenticated_user_can_access_pelanggan_belum_lunas(): void
    {
        $response = $this->actingAs($this->user)->get('/pelanggan-belum-lunas');
        
        $response->assertStatus(200);
    }
}
