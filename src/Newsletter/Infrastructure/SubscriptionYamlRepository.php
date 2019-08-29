<?php
declare(strict_types=1);
namespace App\Newsletter\Infrastructure;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Filesystem\Filesystem;
use App\Newsletter\Domain\Subscription;
use App\Newsletter\Domain\SubscriptionRepository;
use App\Newsletter\Infrastructure\Exception\SubscriptionAlreadyExistsException;
use App\Newsletter\Infrastructure\Exception\SubscriptionNotFoundException;

final class SubscriptionYamlRepository implements SubscriptionRepository
{

    private $filename;

    public function __construct(?string $filename = null)
    {
        $this->setFilename($filename);

        $this->createFile();
    }

    /**
     * @throws SubscriptionAlreadyExistsException
     */
    public function add(Subscription $subscription): void
    {
        $emailAddress = $subscription->getEmailAddress();
        if ($this->subscriptionAlreadyExists($emailAddress)) {
            throw new SubscriptionAlreadyExistsException('Subscription with email ' . $emailAddress . ' already exists');
        }

        $row = [
            'subscription_id' => $subscription->getSubscriptionId(),
            'email_address' => $subscription->getEmailAddress(),
            'creation_date' => $subscription->getCreationDate(),
            'status' => $subscription->getStatus()
        ];

        $rows = [];
        $rows[] = $row;

        file_put_contents($this->filename, Yaml::dump($rows), FILE_APPEND);
    }

    /**
     * @throws SubscriptionNotFoundException
     */
    public function update(Subscription $subscription): void
    {        
        if (!$this->subscriptionAlreadyExists($subscription->getEmailAddress())) {
            throw new SubscriptionNotFoundException('Subscription with email has not been found');
        }

        $this->remove($subscription);

        $this->add($subscription);
    }

    /**
     * Refactor if expression     
     */
    public function getByEmailAddressAndOptionallyBySubscriptionId(string $emailAddress, ?string $subscriptionId = null): Subscription
    {
        foreach ($this->getRows() as $row) {
            if (
                $subscriptionId != null && $this->existsInRow($row, 'subscription_id', $subscriptionId) && $this->existsInRow($row, 'email_address', $emailAddress)
            ) {
                return Subscription::createFromData($row);
            } elseif ($this->existsInRow($row, 'email_address', $emailAddress)) {
                return Subscription::createFromData($row);
            }
        }

        throw new SubscriptionNotFoundException('Subscription with email has not been found');
    }

    private function subscriptionAlreadyExists(string $emailAddress): bool
    {
        foreach ($this->getRows() as $row) {
            if ($this->existsInRow($row, 'email_address', $emailAddress)) {
                return true;
            }
        }

        return false;
    }

    private function remove(Subscription $subscription): void
    {
        $rows = [];
        foreach ($this->getRows() as $row) {
            if ($this->existsInRow($row, 'email_address', $subscription->getEmailAddress())) {
                continue;
            }

            $rows[] = $row;
        }

        file_put_contents($this->filename, count($rows) ? Yaml::dump($rows) : '');
    }

    private function getRows(): array
    {
        return Yaml::parseFile($this->filename) ?: [];
    }

    private function existsInRow(array $row, string $key, $value): bool
    {
        if (isset($row[$key]) && $row[$key] == $value) {
            return true;
        }

        return false;
    }

    private function setFilename(?string $filename = null): void
    {
        $this->filename = null === $filename ? '/var/www/html/app/data/subscription/data.yml' : $filename;
    }

    /**
     * @throws UnexpectedValueException
     */
    private function createFile(): void
    {
        if (!$this->filename) {
            throw new \UnexpectedValueException('Value of a filename expected to be not empty');
        }

        (new Filesystem())->touch($this->filename);
    }
}
