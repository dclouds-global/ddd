<?php

declare(strict_types=1);

namespace Dclouds\Ddd\Contracts\Aggregate\Exceptions;

use RuntimeException;

/**
 * Невозможно изменить версию для немодифицированного агрегата
 */
final class ImpossibleToChangeVersionForUnmodifiedAggregateException extends RuntimeException
{
}
