<?php

declare(strict_types=1);

namespace Dclouds\Ddd\ValueObjects\Email\Exceptions;

use DomainException;

/**
 * Ошибка при создании типа Email
 */
final class FailedEmailCreationException extends DomainException
{
}
