<?php

declare(strict_types=1);

namespace Dclouds\Ddd\ValueObjects\Double\Validator\Exceptions;

use DomainException;

/**
 * Значение типа Double больше максимально допустимого
 */
final class DoubleIsTooBigException extends DomainException
{
}
