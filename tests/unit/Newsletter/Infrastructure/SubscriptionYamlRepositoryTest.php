<?php
declare(strict_types=1);
namespace App\Tests\Unit\Newsletter\Infrastructure;

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use App\Newsletter\Infrastructure\SubscriptionYamlRepository;
use App\Newsletter\Domain\Subscription;
use App\Newsletter\Domain\SubscriptionStatus;
use App\Newsletter\Infrastructure\Exception\SubscriptionAlreadyExistsException;
use App\Newsletter\Infrastructure\Exception\SubscriptionNotFoundException;

class SubscriptionYamlRepositoryTest extends TestCase
{

    private const FIXED_SUBSCRIPTION_ID = 'fixed-subscription-id';
    private const FIXED_EMAIL_ADDRESS = 'krzysztof.kubacki@tratatata.pl';

    private $cacheDir;
    private $repository;
    private $subscription;

    protected function setUp()
    {
        $this->cacheDir = vfsStream::setup('cache');
        $this->repository = new SubscriptionYamlRepository(vfsStream::url('cache/data.yml'));

        $this->subscription = $this->createSubscription();
    }

    public function testSavingNewSubscription(): void
    {
        $this->repository->add($this->subscription);

        $this->assertContains(
            'subscription_id: ' . self::FIXED_SUBSCRIPTION_ID
            , $this->cacheDir->getChild('data.yml')->getContent()
        );

        $this->assertContains(
            'email_address: ' . self::FIXED_EMAIL_ADDRESS
            , $this->cacheDir->getChild('data.yml')->getContent()
        );

        $this->assertRegExp(
            '/creation_date:\s\'\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\'/'
            , $this->cacheDir->getChild('data.yml')->getContent()
        );

        $this->assertContains(
            'status: ' . SubscriptionStatus::DEFAULT()
            , $this->cacheDir->getChild('data.yml')->getContent()
        );
    }

    public function testIfFailsSavingNewSubscriptionWhenSubscriptionWithEmailAddressAlreadyExists(): void
    {
        $this->expectException(SubscriptionAlreadyExistsException::class);

        $this->repository->add($this->subscription);

        $this->subscription = $this->createSubscription();

        $this->repository->add($this->subscription);
    }

    public function testUpdatingSubscription(): void
    {
        $this->repository->add($this->subscription);

        $this->subscription->confirm(self::FIXED_SUBSCRIPTION_ID, self::FIXED_EMAIL_ADDRESS);

        $this->repository->update($this->subscription);

        $subscription = $this->repository->getByEmailAddressAndOptionallyBySubscriptionId(self::FIXED_EMAIL_ADDRESS, self::FIXED_SUBSCRIPTION_ID);

        $this->assertEquals($subscription->getStatus(), SubscriptionStatus::CONFIRMED());
        $this->assertNotContains(
            'status: ' . SubscriptionStatus::DEFAULT()
            , $this->cacheDir->getChild('data.yml')->getContent()
        );
    }

    public function testIfFailsSubscriptionUpdatingWhenRepositoryIsEmpty(): void
    {
        $this->expectException(SubscriptionNotFoundException::class);

        $this->repository->update($this->subscription);
    }

    public function testIfFailsSubscriptionUpdatingWhenSubscriptionIsNotPersistedInRepository(): void
    {
        $this->expectException(SubscriptionNotFoundException::class);

        $this->repository->add($this->subscription);

        $this->repository->update(
            Subscription::create('fake-email-address@tralalala.pl', 'fake-subscription-id')
        );
    }

    public function testFindingSubscriptionByEmailAddress(): void
    {
        $this->repository->add($this->subscription);

        $this->assertInstanceOf(
            Subscription::class,
            $this->repository->getByEmailAddressAndOptionallyBySubscriptionId(self::FIXED_EMAIL_ADDRESS)
        );
    }
    
    public function testFindingSubscriptionByEmailAddressAndSubscriptionId(): void
    {
        $this->repository->add($this->subscription);

        $this->assertInstanceOf(
            Subscription::class,
            $this->repository->getByEmailAddressAndOptionallyBySubscriptionId(self::FIXED_EMAIL_ADDRESS, self::FIXED_SUBSCRIPTION_ID)
        );
    }

    public function testIfFailsFindingSubscriptionInEmptyRepository(): void
    {
        $this->expectException(SubscriptionNotFoundException::class);

        $this->repository->getByEmailAddressAndOptionallyBySubscriptionId(self::FIXED_EMAIL_ADDRESS, self::FIXED_SUBSCRIPTION_ID);
    }

    private function createSubscription(): Subscription
    {
        return Subscription::create(
                self::FIXED_EMAIL_ADDRESS,
                self::FIXED_SUBSCRIPTION_ID
        );
    }
}
