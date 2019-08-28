<?php
namespace App\Tests\Features\Newsletter;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use org\bovigo\vfs\vfsStream;
use Webmozart\Assert\Assert;
use App\Newsletter\Application\CreateSubscriptionCommand;
use App\Newsletter\Application\CreateSubscriptionHandler;
use App\Newsletter\Infrastructure\SubscriptionYamlRepository;
use App\Newsletter\Infrastructure\Exception\SubscriptionAlreadyExistsException;

/**
 * Defines application features from the specific context.
 */
class SubscriptionCreationContext implements Context
{

    private $emailAddress;
    private $repository;

    /** @BeforeScenario */
    public function setUp(): void
    {
        $this->cacheDir = vfsStream::setup('cache');
        $this->repository = new SubscriptionYamlRepository(vfsStream::url('cache/data.yml'));
    }

    /**
     * @Given a user has the :arg1 email address
     */
    public function setEmailAddress(string $emailAddress): void
    {
        $this->emailAddress = $emailAddress;
    }

    /**
     * @When the user subscribe in a newsletter
     * @Given the user has an unconfirmed subscription already
     */
    public function createSubscription(): void
    {
        $command = new CreateSubscriptionCommand($this->emailAddress);
        $handler = new CreateSubscriptionHandler($this->repository);
        $handler->handle($command);
    }

    /**
     * @Then a new newsletter subscription should be created
     */
    public function checkIfSubscriptionCreated(): void
    {
        $subscription = $this->repository->getByEmailAddressAndOptionallyBySubscriptionId($this->emailAddress);
        Assert::eq($this->emailAddress, $subscription->getEmailAddress());
    }

    /**
     * @When the user subscribe in a newsletter with the same email address the subscription should fail
     */
    public function createSubscriptionWhenSubscriptionAlreadyExists(): void
    {
        try {
            $this->createSubscription();
            throw new \Exception();
        } catch (SubscriptionAlreadyExistsException $ex) {
            Assert::true(true);
        } catch (\Exception $ex) {
            Assert::true(false);
        }
    }
}
