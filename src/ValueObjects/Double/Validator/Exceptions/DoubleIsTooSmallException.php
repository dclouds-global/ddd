<?php

declare(strict_types=1);

namespace Dclouds\Ddd\ValueObjects\Double\Validator\Exceptions;

use DomainException;

/**
 * Значение типа Double меньше минимально допустимого
 */
final class DoubleIsTooSmallException extends DomainException
{
}
