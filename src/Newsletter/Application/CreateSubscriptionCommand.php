<?php
declare(strict_types=1);
namespace App\Newsletter\Application;

final class CreateSubscriptionCommand
{

    private $emailAddress;
    private $subscriptionId;

    public function __construct(string $emailAddress, ?string $subscriptionId = null)
    {
        $this->emailAddress = $emailAddress;
        $this->subscriptionId = $subscriptionId;
    }

    public function emailAddress(): string
    {
        return $this->emailAddress;
    }

    public function subscriptionId(): ?string
    {
        return $this->subscriptionId;
    }
}
