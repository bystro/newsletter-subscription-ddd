<?php
declare(strict_types=1);
namespace App\Newsletter\Domain;

use App\Newsletter\Domain\Subscription;

interface SubscriptionRepository
{
    public function add(Subscription $subscription): void;

    public function update(Subscription $subscription): void;

    public function getByEmailAddressAndOptionallyBySubscriptionId(string $emailAddress, string $subscriptionId = ''): ?Subscription;
}
