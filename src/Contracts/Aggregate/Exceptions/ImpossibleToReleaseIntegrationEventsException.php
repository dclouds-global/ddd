<?php

declare(strict_types=1);

namespace Dclouds\Ddd\Contracts\Aggregate\Exceptions;

use RuntimeException;

/**
 * Нельзя выпустить интеграционные события, пока не были выпущены
 * события предметной области
 */
final class ImpossibleToReleaseIntegrationEventsException extends RuntimeException
{
}
