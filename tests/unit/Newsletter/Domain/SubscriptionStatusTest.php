<?php
declare(strict_types=1);
namespace App\Tests\Unit\Newsletter\Domain;

use PHPUnit\Framework\TestCase;
use App\Newsletter\Domain\SubscriptionStatus;

class SubscriptionStatusTest extends TestCase
{

    public function testDefaultStatus(): void
    {
        $this->assertNotEmpty(SubscriptionStatus::DEFAULT());
    }

    public function testConfirmedStatus(): void
    {
        $this->assertNotEmpty(SubscriptionStatus::CONFIRMED());
    }

    public function testValueOfSubscriptionStatusCastedToString(): void
    {
        $this->assertEquals(SubscriptionStatus::DEFAULT(), (string) SubscriptionStatus::DEFAULT());
    }
}
