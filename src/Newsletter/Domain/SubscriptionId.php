<?php
declare(strict_types=1);
namespace App\Newsletter\Domain;

use App\ValueObject;
use Ramsey\Uuid\Uuid;

final class SubscriptionId implements ValueObject
{

    private $id;

    public function __construct(?string $id = null)
    {
        $this->id = null === $id ? Uuid::uuid4()->toString() : $id;
    }

    public function id(): string
    {
        return $this->id;
    }
    
    public function equals(SubscriptionId $subscriptionId): bool
    {
        return $this->id() === $subscriptionId->id();
    }
    
    public function __toString(): string
    {
        return $this->id;
    }
}
