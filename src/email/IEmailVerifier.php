<?php
declare(strict_types=1);

namespace DeveloperNode\Email;

interface IEmailVerifier
{
    function CheckEmail(string $email): CheckEmailStatus;
}