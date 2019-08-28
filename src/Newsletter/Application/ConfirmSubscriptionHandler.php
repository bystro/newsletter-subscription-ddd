<?php
declare(strict_types=1);
namespace App\Newsletter\Application;

use App\Newsletter\Domain\SubscriptionRepository;
use App\Newsletter\Application\ConfirmSubscriptionCommand;

final class ConfirmSubscriptionHandler
{

    private $repository;

    public function __construct(SubscriptionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(ConfirmSubscriptionCommand $command): void
    {
        $subscription = $this->repository->getByEmailAddressAndOptionallyBySubscriptionId(
            $command->emailAddress(),
            $command->subscriptionId()
        );

        $subscription->confirm($command->subscriptionId(), $command->emailAddress());

        $this->repository->update($subscription);
    }
}
