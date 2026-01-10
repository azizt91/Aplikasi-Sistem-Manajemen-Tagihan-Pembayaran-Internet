<?php

namespace Tests\Unit;

use App\Services\TagihanService;
use Tests\TestCase;

/**
 * Unit tests untuk TagihanService.
 * 
 * Test class ini menguji business logic di TagihanService
 * tanpa interaksi database untuk test sederhana.
 */
class TagihanServiceTest extends TestCase
{
    protected TagihanService $tagihanService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tagihanService = new TagihanService();
    }

    /**
     * Test: Dapat mengambil nama bulan dalam Bahasa Indonesia.
     */
    public function test_can_get_month_names(): void
    {
        $monthNames = TagihanService::getMonthNames();

        $this->assertCount(12, $monthNames);
        $this->assertEquals('Januari', $monthNames[1]);
        $this->assertEquals('Desember', $monthNames[12]);
    }

    /**
     * Test: Dapat mengambil nama bulan spesifik.
     */
    public function test_can_get_specific_month_name(): void
    {
        $this->assertEquals('Januari', TagihanService::getMonthName(1));
        $this->assertEquals('Juni', TagihanService::getMonthName(6));
        $this->assertEquals('Desember', TagihanService::getMonthName(12));
        $this->assertEquals('', TagihanService::getMonthName(13)); // Invalid month
    }

    /**
     * Test: Array bulan memiliki 12 elemen.
     */
    public function test_month_names_has_twelve_elements(): void
    {
        $this->assertCount(12, TagihanService::MONTH_NAMES);
    }

    /**
     * Test: Bulan pertama adalah Januari.
     */
    public function test_first_month_is_januari(): void
    {
        $this->assertEquals('Januari', TagihanService::MONTH_NAMES[1]);
    }

    /**
     * Test: Bulan terakhir adalah Desember.
     */
    public function test_last_month_is_desember(): void
    {
        $this->assertEquals('Desember', TagihanService::MONTH_NAMES[12]);
    }

    /**
     * Test: Semua bulan memiliki nama yang tidak kosong.
     */
    public function test_all_months_have_names(): void
    {
        foreach (TagihanService::MONTH_NAMES as $month => $name) {
            $this->assertNotEmpty($name, "Month {$month} should have a name");
            $this->assertIsString($name, "Month {$month} name should be a string");
        }
    }
}
