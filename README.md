# Email Verification Library
Using this library, you can verify if email entered is real or not. By real it is meant that it will be validated not just in a synctactic way, but also against SMTP server.

## Installation

Through composer: *composer require developernode/email-verify*

## Usage

```php
use DeveloperNode\Email\EmailVerifier;

// ...

$verifier = new EmailVerifier();
$verifier->CheckEmail("me@invalidemaildomain.com");
$verifier->CheckEmail("vaidotas@developernode.net");
$verifier->CheckEmail("i_do_not_exist@developernode.net");

// ...
```

## Notes
EmailVerifier implements IEmailVerifier interface, thus it will be easier to mock logic if required, and integrate it into your own code without violating SOLID development guidelines.
