<?php
namespace App\Tests\Features\Newsletter;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use org\bovigo\vfs\vfsStream;
use Webmozart\Assert\Assert;
use App\Newsletter\Application\CreateSubscriptionCommand;
use App\Newsletter\Application\CreateSubscriptionHandler;
use App\Newsletter\Application\ConfirmSubscriptionCommand;
use App\Newsletter\Application\ConfirmSubscriptionHandler;
use App\Newsletter\Infrastructure\SubscriptionYamlRepository;
use App\Newsletter\Domain\SubscriptionStatus;
use App\Newsletter\Domain\Exception\SubscriptionAlreadyConfirmedException;
use App\Newsletter\Domain\Exception\SubscriptionInvalidConfirmationArgumentException;
use App\Newsletter\Infrastructure\Exception\SubscriptionNotFoundException;

/**
 * Defines application features from the specific context.
 */
class SubscriptionConfirmationContext implements Context
{

    private $subscription;
    private $repository;

    /** @BeforeScenario */
    public function setUp(): void
    {
        $this->cacheDir = vfsStream::setup('cache');
        $this->repository = new SubscriptionYamlRepository(vfsStream::url('cache/data.yml'));
    }

    /**
     * @Given a user created a subscription using the :emailAddress email address and the :subscriptionId subscription id
     */
    public function createSubscription(string $emailAddress, string $subscriptionId): void
    {
        $command = new CreateSubscriptionCommand($emailAddress, $subscriptionId);
        $handler = new CreateSubscriptionHandler($this->repository);
        $handler->handle($command);
        
        $this->subscription = $this->repository->getByEmailAddressAndOptionallyBySubscriptionId($emailAddress);
    }

    /**
     * @When the user confirms the subscription using the :emailAddress email address and the :subscriptionId subscription id
     */
    public function confirmSubscription(string $emailAddress, string $subscriptionId): void
    {
        $this->executeConfirmationCommand(
            new ConfirmSubscriptionCommand($subscriptionId, $emailAddress)
        );
    }

    /**
     * @Then the subscription should be confirmed
     */
    public function checkIfSubscriptionIsConfirmed(): void
    {
        $subscription = $this->repository->getByEmailAddressAndOptionallyBySubscriptionId($this->subscription->getEmailAddress());
        Assert::eq($subscription->getStatus(), SubscriptionStatus::CONFIRMED());
    }

    /**
     * @When the user confirms the subscription using the :emailAddress email address and the :subscriptionId subscription id then confirmation should fail
     */
    public function confirmAlreadyConfirmedSubscription(string $emailAddress, string $subscriptionId): void
    {
        try {
            $this->executeConfirmationCommand(
                new ConfirmSubscriptionCommand($subscriptionId, $emailAddress)
            );

            throw new \Exception();
        } catch (SubscriptionAlreadyConfirmedException $ex) {
            Assert::true(true);
        } catch (\Exception $ex) {
            Assert::true(false);
        }
    }

    /**
     * @When the user confirms the subscription using not existing :subscriptionId subscription id then confirmation should fail
     */
    public function confirmSubscriptionUsingNotExistingSubscriptionId(string $subscriptionId): void
    {
        try {
            $this->executeConfirmationCommand(
                new ConfirmSubscriptionCommand($subscriptionId, $this->subscription->getEmailAddress())
            );

            throw new \Exception();
        } catch (SubscriptionInvalidConfirmationArgumentException $ex) {
            Assert::true(true);
        } catch (\Exception $ex) {
            Assert::true(false);
        }
    }

    /**
     * @When the user confirms the subscription using not existing :emailAddress email address then confirmation should fail
     */
    public function confirmSubscriptionUsingNotExistingEmailAddress(string $emailAddress): void
    {
        try {
            $this->executeConfirmationCommand(
                new ConfirmSubscriptionCommand($this->subscription->getSubscriptionId(), $emailAddress)
            );

            throw new \Exception();
        } catch (SubscriptionNotFoundException $ex) {
            Assert::true(true);
        } catch (\Exception $ex) {
            Assert::true(false);
        }
    }

    private function executeConfirmationCommand(ConfirmSubscriptionCommand $command): void
    {
        $handler = new ConfirmSubscriptionHandler($this->repository);
        $handler->handle($command);
    }
}
