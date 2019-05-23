<?php
declare(strict_types=1);

namespace DeveloperNode\Email;

use \MyCLabs\Enum\Enum;

/**
 * @method static self SUCCESS()
 * @method static self INVALID_EMAIL()
 * @method static self EMAIL_NOT_FOUND()
 * @method static self INVALID_EMAIL_DOMAIN()
 */
class CheckEmailStatus extends Enum
{
    const SUCCESS = 'success';
    const INVALID_EMAIL = 'invalid_email';
    const EMAIL_NOT_FOUND = 'email_not_found';
    const INVALID_EMAIL_DOMAIN = 'invalid_email_domain';
}