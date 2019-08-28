<?php
declare(strict_types=1);
namespace App\Newsletter\Application;

final class ConfirmSubscriptionCommand
{

    private $subscriptionId;
    private $emailAddress;

    public function __construct(string $subscriptionId, string $emailAddress)
    {
        $this->subscriptionId = $subscriptionId;
        $this->emailAddress = $emailAddress;
    }

    public function subscriptionId(): string
    {
        return $this->subscriptionId;
    }

    public function emailAddress(): string
    {
        return $this->emailAddress;
    }
}
