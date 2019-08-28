<?php
declare(strict_types=1);
namespace App\Tests\Unit\Newsletter\Domain;

use PHPUnit\Framework\TestCase;
use App\Newsletter\Domain\Subscription;
use App\Newsletter\Domain\SubscriptionStatus;
use App\Newsletter\Domain\Exception\SubscriptionAlreadyConfirmedException;
use App\Newsletter\Domain\Exception\SubscriptionInvalidConfirmationArgumentException;

class SubscriptionTest extends TestCase
{

    private const FIXED_SUBSCRIPTION_ID = 'fixed-subscription-id';
    private const FIXED_EMAIL_ADDRESS = 'krzysztof.kubacki@tratatata.pl';

    private $subscription;

    protected function setUp()
    {
        $this->subscription = Subscription::create(self::FIXED_EMAIL_ADDRESS, self::FIXED_SUBSCRIPTION_ID);
    }

    public function testCreatingSubscriptionFromInputData(): void
    {
        $input = [
            'subscription_id' => self::FIXED_SUBSCRIPTION_ID,
            'email_address' => self::FIXED_EMAIL_ADDRESS,
            'creation_date' => '2019-08-27 09:45:44',
            'status' => 'confirmed'
        ];

        $subscription = Subscription::createFromData($input);

        $this->assertEquals($input['subscription_id'], $subscription->getSubscriptionId());
        $this->assertEquals($input['email_address'], $subscription->getEmailAddress());
        $this->assertEquals($input['creation_date'], $subscription->getCreationDate());
        $this->assertEquals($input['status'], $subscription->getStatus());
    }

    public function testSubscriptionStatusWhenSubscriptionCreated(): void
    {
        $this->assertEquals($this->subscription->getStatus(), SubscriptionStatus::DEFAULT());
    }

    public function testSubscriptionSuccessConfirmation(): void
    {
        $this->assertEquals($this->subscription->getStatus(), SubscriptionStatus::DEFAULT());

        $this->subscription->confirm(self::FIXED_SUBSCRIPTION_ID, self::FIXED_EMAIL_ADDRESS);
        $this->assertEquals($this->subscription->getStatus(), SubscriptionStatus::CONFIRMED());
    }

    public function testIfConfirmationFailsWhenInvalidSubscriptionIdGiven(): void
    {
        $this->expectException(SubscriptionInvalidConfirmationArgumentException::class);

        $this->assertEquals($this->subscription->getStatus(), SubscriptionStatus::DEFAULT());

        $this->subscription->confirm('fake-subscription-id', self::FIXED_EMAIL_ADDRESS);
    }

    public function testIfConfirmationFailsWhenInvalidEmailAddressGiven(): void
    {
        $this->expectException(SubscriptionInvalidConfirmationArgumentException::class);

        $this->assertEquals($this->subscription->getStatus(), SubscriptionStatus::DEFAULT());

        $this->subscription->confirm(self::FIXED_SUBSCRIPTION_ID, 'fake-email-address');
    }

    public function testIfConfirmationFailsWhenInvalidSubscriptionIdAndEmailAddressGiven(): void
    {
        $this->expectException(SubscriptionInvalidConfirmationArgumentException::class);

        $this->assertEquals($this->subscription->getStatus(), SubscriptionStatus::DEFAULT());

        $this->subscription->confirm('fake-subscription-id', 'fake-email-address');
    }

    public function testIfConfirmationFailsForAlreadyConfirmedSubscription(): void
    {
        $this->expectException(SubscriptionAlreadyConfirmedException::class);

        $this->assertEquals($this->subscription->getStatus(), SubscriptionStatus::DEFAULT());

        $this->subscription->confirm(self::FIXED_SUBSCRIPTION_ID, self::FIXED_EMAIL_ADDRESS);
        $this->assertEquals($this->subscription->getStatus(), SubscriptionStatus::CONFIRMED());

        $this->subscription->confirm(self::FIXED_SUBSCRIPTION_ID, self::FIXED_EMAIL_ADDRESS);
    }
}
