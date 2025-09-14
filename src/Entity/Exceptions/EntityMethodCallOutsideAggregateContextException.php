<?php

declare(strict_types=1);

namespace Dclouds\Ddd\Entity\Exceptions;

use RuntimeException;

/**
 * Метод обработки события бизнес-сущности вызван за пределами агрегата этой сущности
 */
final class EntityMethodCallOutsideAggregateContextException extends RuntimeException
{
}
