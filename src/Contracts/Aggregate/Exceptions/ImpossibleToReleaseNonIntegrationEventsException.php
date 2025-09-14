<?php

declare(strict_types=1);

namespace Dclouds\Ddd\Contracts\Aggregate\Exceptions;

use RuntimeException;

/**
 * Нельзя выпустить события, которые не являются интеграционными
 */
final class ImpossibleToReleaseNonIntegrationEventsException extends RuntimeException
{
}
