<?php

namespace Tests\Unit;

use App\Models\Pengembalian;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_that_true_is_true(): void
    {
        $this->assertTrue(true);
    }

    public function test_late_return_fee_is_five_thousand_per_calendar_day(): void
    {
        $this->assertSame(0, Pengembalian::hitungDendaTerlambat('2026-10-05', '2026-10-05'));
        $this->assertSame(0, Pengembalian::hitungDendaTerlambat('2026-10-05', '2026-10-04'));
        $this->assertSame(5000, Pengembalian::hitungDendaTerlambat('2026-10-05', '2026-10-06'));
        $this->assertSame(15000, Pengembalian::hitungDendaTerlambat('2026-10-05', '2026-10-08'));
    }
}
