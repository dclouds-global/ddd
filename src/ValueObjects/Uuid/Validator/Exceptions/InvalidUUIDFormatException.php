<?php

declare(strict_types=1);

namespace Dclouds\Ddd\ValueObjects\Uuid\Validator\Exceptions;

use DomainException;

/**
 * Некорректный формат Uuid
 */
final class InvalidUUIDFormatException extends DomainException
{
}
