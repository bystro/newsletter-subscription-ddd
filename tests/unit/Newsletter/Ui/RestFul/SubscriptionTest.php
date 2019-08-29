<?php
declare(strict_types=1);
namespace App\Tests\Unit\Newsletter\Ui\RestFul;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class SubscriptionTest extends TestCase
{

    private const FIXED_SUBSCRIPTION_ID = 'fixed-subscription-id';
    private const FIXED_EMAIL_ADDRESS = 'krzysztof.kubacki@tratatata.pl';

    protected $client;

    protected function setUp()
    {
        $this->client = new Client([
            'base_uri' => 'http://172.18.0.22',
            'http_errors' => false
        ]);
    }

    public function testIfCreationFailsWhenNoEmailAddressGiven(): void
    {
        $params = [
            'form_params' => [
            ]
        ];

        $response = $this->client->post('/api/newsletter/subscription/create', $params);

        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testIfCreationFailsWhenEmailAddressGivenIsEmpty(): void
    {
        $params = [
            'form_params' => [
                'email_address' => ''
            ]
        ];

        $response = $this->client->post('/api/newsletter/subscription/create', $params);

        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testIfConfirmationFailsWhenNoSubscriptionIdGiven(): void
    {
        $params = [
            'query' => [
                'email_address' => self::FIXED_EMAIL_ADDRESS
            ]
        ];
        $response = $this->client->get('/api/newsletter/subscription/confirm', $params);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testIfConfirmationFailsWhenSubscriptionIdIsEmpty(): void
    {
        $params = [
            'query' => [
                'subscription_id' => '',
                'email_address' => self::FIXED_EMAIL_ADDRESS
            ]
        ];
        $response = $this->client->get('/api/newsletter/subscription/confirm', $params);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testIfConfirmationFailsWhenNoAddressEmailGiven(): void
    {
        $params = [
            'query' => [
                'subscription_id' => self::FIXED_SUBSCRIPTION_ID,
            ]
        ];
        $response = $this->client->get('/api/newsletter/subscription/confirm', $params);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testIfConfirmationFailsWhenAddressEmailIsEmpty(): void
    {
        $params = [
            'query' => [
                'subscription_id' => self::FIXED_SUBSCRIPTION_ID,
                'email_address' => ''
            ]
        ];
        $response = $this->client->get('/api/newsletter/subscription/confirm', $params);

        $this->assertEquals(400, $response->getStatusCode());
    }
}
