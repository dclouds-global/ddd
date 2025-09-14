<?php

declare(strict_types=1);

namespace Dclouds\Ddd\Contracts\Aggregate\Exceptions;

use RuntimeException;

/**
 * Невозможно выпустить события предметной области без обновления версии агрегата
 */
final class ImpossibleToReleaseDomainEventsWithoutAggregateVersionUpdateException extends RuntimeException
{
}
