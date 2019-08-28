<?php
declare(strict_types=1);
namespace App\Newsletter\Domain;

use App\Newsletter\Domain\SubscriptionId;
use App\Newsletter\Domain\EmailAddress;
use App\Newsletter\Domain\SubscriptionStatus;
use App\Newsletter\Domain\Exception\SubscriptionAlreadyConfirmedException;
use App\Newsletter\Domain\Exception\SubscriptionInvalidConfirmationArgumentException;

final class Subscription
{

    private $subscriptionId;
    private $emailAddress;
    private $creationDate;
    private $status;

    public function __construct(SubscriptionId $subscriptionId, EmailAddress $emailAddress)
    {
        $this->setSubscriptionId($subscriptionId);
        $this->setEmailAddress($emailAddress);
        $this->setCreationDate();
        $this->status = SubscriptionStatus::DEFAULT();
    }

    public function getSubscriptionId(): string
    {
        return (string) $this->subscriptionId;
    }

    public function getEmailAddress(): string
    {
        return (string) $this->emailAddress;
    }

    public function getCreationDate(): string
    {
        return $this->creationDate->format('Y-m-d H:i:s');
    }

    public function getStatus(): string
    {
        return (string) $this->status;
    }

    /**
     * @throws \LogicException
     */
    public function confirm(string $subscriptionId, string $emailAddress): void
    {
        if ($this->confirmed()) {
            throw new SubscriptionAlreadyConfirmedException('The subscription has been confirmed before');
        }

        if ($this->getSubscriptionId() != $subscriptionId || $this->getEmailAddress() != $emailAddress) {
            throw new SubscriptionInvalidConfirmationArgumentException('The subscription has not been confirmed');
        }

        $this->status = SubscriptionStatus::CONFIRMED();
    }

    public static function create(string $emailAddress, ?string $subscriptionId = null): self
    {
        return new self(
            new SubscriptionId($subscriptionId),
            new EmailAddress($emailAddress)
        );
    }

    public static function createFromData(array $data): Subscription
    {
        $reflection = new \ReflectionClass(self::class);
        $subscription = $reflection->newInstanceWithoutConstructor();
        
        $subscription->subscriptionId = new SubscriptionId($data['subscription_id']);
        $subscription->emailAddress = new EmailAddress($data['email_address']);
        $subscription->creationDate = new \DateTimeImmutable($data['creation_date']);
        $subscription->status = $data['status'];
        
        return $subscription;
    }

    private function confirmed(): bool
    {
        return $this->getStatus() == SubscriptionStatus::CONFIRMED();
    }

    private function setSubscriptionId(SubscriptionId $subscriptionId): void
    {
        $this->subscriptionId = $subscriptionId;
    }

    private function setEmailAddress(EmailAddress $emailAddress): void
    {
        $this->emailAddress = $emailAddress;
    }

    private function setCreationDate(): void
    {
        $this->creationDate = new \DateTimeImmutable();
    }
}
