<?php

declare(strict_types=1);

namespace Dclouds\Ddd\ValueObjects\Uuid\Exceptions;

use DomainException;

/**
 * Ошибка при создании идентификатора
 */
final class FailedUuidCreationException extends DomainException
{
}
