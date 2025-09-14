<?php

declare(strict_types=1);

namespace Dclouds\Ddd\Contracts\Aggregate\Exceptions;

use RuntimeException;

/**
 * Недопустимая версия агрегата
 */
final class IncorrectAggregateVersionException extends RuntimeException
{
}
