<?php

declare(strict_types=1);

namespace Dclouds\Ddd\ValueObjects\Amount\Validator\Exceptions;

use DomainException;

/**
 * Сумма меньше минимально допустимого значения
 */
final class AmountIsTooSmallException extends DomainException
{
}
