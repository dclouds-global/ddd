<?php

declare(strict_types=1);

namespace Dclouds\Ddd\ValueObjects\Email\Validator\Exceptions;

use DomainException;

/**
 * Email имеет невалидный адрес
 */
final class EmailMustHasValidAddressException extends DomainException
{
}
