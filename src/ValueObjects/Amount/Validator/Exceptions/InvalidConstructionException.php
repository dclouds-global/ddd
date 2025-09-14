<?php

declare(strict_types=1);

namespace Dclouds\Ddd\ValueObjects\Amount\Validator\Exceptions;

use InvalidArgumentException;

/**
 * Некорректные параметры валидатора
 */
final class InvalidConstructionException extends InvalidArgumentException
{
}
