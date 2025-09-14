<?php

declare(strict_types=1);

namespace Dclouds\Ddd\ValueObjects\Amount\Exceptions;

use DomainException;

/**
 * Ошибка при создании типа Amount
 */
final class FailedAmountCreationException extends DomainException
{
}
