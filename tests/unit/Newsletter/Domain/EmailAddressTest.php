<?php
declare(strict_types=1);
namespace App\Tests\Unit\Newsletter\Domain;

use PHPUnit\Framework\TestCase;
use App\Newsletter\Domain\EmailAddress;

class EmailAddressTest extends TestCase
{

    public function testCreating(): void
    {
        $this->assertInstanceOf(EmailAddress::class, new EmailAddress('krzysztof.kubacki@tratatata.pl'));
    }

    public function testFailsWhenCreatingWithInvalidEmailAddress(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new EmailAddress('This is not a valid emaill address');
    }

    public function testIfOneEmailAddressEqualsAnother(): void
    {
        $theSameEmailForBoth = 'krzysztof.kubacki@tratatata.pl';
        $emailAddress = new EmailAddress($theSameEmailForBoth);

        $this->assertTrue(
            $emailAddress->equals(
                new EmailAddress($theSameEmailForBoth)
            )
        );
    }

    public function testIfOneEmailAddressNotEqualsAnother(): void
    {
        $emailAddress = new EmailAddress('krzysztof.kubacki@tratatata.pl');

        $this->assertFalse(
            $emailAddress->equals(
                new EmailAddress('different-address-email@tratatata.pl')
            )
        );
    }

    public function testValueOfEmailAddressCastedToString(): void
    {
        $email = 'krzysztof.kubacki@tratatata.pl';

        $this->assertEquals($email, (string) new EmailAddress($email));
    }
}
