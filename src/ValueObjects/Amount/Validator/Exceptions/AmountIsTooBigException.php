<?php

declare(strict_types=1);

namespace Dclouds\Ddd\ValueObjects\Amount\Validator\Exceptions;

use DomainException;

/**
 * Сумма больше максимально допустимого значения
 */
final class AmountIsTooBigException extends DomainException
{
}
