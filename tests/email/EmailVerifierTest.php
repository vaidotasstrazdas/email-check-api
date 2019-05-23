<?php

declare(strict_types=1);

namespace DeveloperNode\Test\Email;

use PHPUnit\Framework\TestCase;

use DeveloperNode\Email\CheckEmailStatus;
use DeveloperNode\Email\EmailVerifier;

class EmailVerifierTest extends TestCase
{
    private $verifier;

    protected function setUp()
    {
        $this->verifier = new EmailVerifier();
    }
    
    public function testInvalidEmailIsTreatedAsSuch()
    {
        // Act
        $result = $this->verifier->CheckEmail("invalid_email_string");
        
        // Assert
        $this->assertEquals(
            CheckEmailStatus::INVALID_EMAIL(),
            $result
        );
    }
    
    public function testInvalidEmailDomainIsTreatedAsSuch()
    {
        // Act
        $result = $this->verifier->CheckEmail("me@invalidemaildomain.com");
        
        // Assert
        $this->assertEquals(
            CheckEmailStatus::INVALID_EMAIL_DOMAIN(),
            $result
        );
    }
    
    public function testExistingEmailIsReturnedAsSuch()
    {
        // Act
        $result = $this->verifier->CheckEmail("vaidotas@developernode.net");
        
        // Assert
        $this->assertEquals(
            CheckEmailStatus::SUCCESS(),
            $result
        );
    }
    
    public function testNotExistingEmailIsReturnedAsSuch()
    {
        // Act
        $result = $this->verifier->CheckEmail("i_do_not_exist@developernode.net");
        
        // Assert
        $this->assertEquals(
            CheckEmailStatus::EMAIL_NOT_FOUND(),
            $result
        );
    }
}