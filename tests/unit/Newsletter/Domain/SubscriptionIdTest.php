<?php
declare(strict_types=1);
namespace App\Tests\Unit\Newsletter\Domain;

use PHPUnit\Framework\TestCase;
use App\Newsletter\Domain\SubscriptionId;

class SubscriptionIdTest extends TestCase
{

    public function testCreatingWithRandomValue(): void
    {
        $this->assertInstanceOf(SubscriptionId::class, new SubscriptionId());
    }

    public function testCreatingWithValue(): void
    {
        $id = 'subscription-value';
        $subscriptionId = new SubscriptionId($id);

        $this->assertInstanceOf(SubscriptionId::class, $subscriptionId);
        $this->assertEquals($id, $subscriptionId->id());
    }

    public function testIfOneSubscriptionEqualsAnother(): void
    {
        $theSameIdForBoth = 'krzysztof.kubacki@tratatata.pl';
        $subscriptionId = new SubscriptionId($theSameIdForBoth);

        $this->assertTrue(
            $subscriptionId->equals(
                new SubscriptionId($theSameIdForBoth)
            )
        );
    }

    public function testIfOneSubscriptionNotEqualsAnother(): void
    {
        $subscriptionId = new SubscriptionId();

        $this->assertFalse(
            $subscriptionId->equals(
                new SubscriptionId()
            )
        );
    }

    public function testValueOfSubscriptionIdCastedToString(): void
    {
        $id = 'subscription-value';

        $this->assertEquals($id, (string) new SubscriptionId($id));
    }
}
