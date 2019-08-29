<?php
declare(strict_types=1);
namespace App\Newsletter\Application;

use App\Newsletter\Domain\SubscriptionRepository;
use App\Newsletter\Application\CreateSubscriptionCommand;
use App\Newsletter\Domain\Subscription;

final class CreateSubscriptionHandler
{

    private $subscriptionRepository;

    public function __construct(SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function __invoke(CreateSubscriptionCommand $command): void
    {
        $this->subscriptionRepository->add(
            Subscription::create(
                $command->emailAddress(),
                $command->subscriptionId()
            )
        );
    }
}
