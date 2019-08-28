<?php
declare(strict_types=1);
namespace App\Newsletter\Domain;

use App\ValueObject;
use Webmozart\Assert\Assert;

final class EmailAddress implements ValueObject
{

    private $email;

    public function __construct(string $email)
    {
        $this->setEmail($email);
    }

    public function equals(EmailAddress $emailAddress): bool
    {
        return $this->email === $emailAddress->email;
    }

    public function __toString(): string
    {
        return $this->email;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function setEmail(string $email): void
    {
        Assert::string($email);

        Assert::email($email);

        $this->email = $email;
    }
}
