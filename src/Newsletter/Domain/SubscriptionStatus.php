<?php
declare(strict_types=1);
namespace App\Newsletter\Domain;

use MyCLabs\Enum\Enum;
use App\ValueObject;

final class SubscriptionStatus extends Enum implements ValueObject
{

    private const DEFAULT = 'new';
    private const CONFIRMED = 'confirmed';

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
