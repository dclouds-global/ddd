<?php

declare(strict_types=1);

namespace Dclouds\Ddd\Contracts\Aggregate\Exceptions;

use RuntimeException;

/**
 * Применяемое событие не реализовано в агрегате
 */
final class DomainEventNotImplementedException extends RuntimeException
{
}
