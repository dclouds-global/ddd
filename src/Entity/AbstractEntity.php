<?php

declare(strict_types=1);

namespace Dclouds\Ddd\Entity;

use Dclouds\Ddd\Contracts\Aggregate\DomainEventInterface;
use Dclouds\Ddd\Contracts\Aggregate\Exceptions\DomainEventNotImplementedException;
use Dclouds\Ddd\Entity\Exceptions\EntityMethodCallOutsideAggregateContextException;

/**
 * Базовый класс бизнес-сущности
 */
abstract class AbstractEntity
{
    /**
     * Имя класса агрегата с указанием неймспейса.
     */
    abstract protected string $aggregateClass {get;}

    /**
     * Применяет событие предметной области к сущности, изменяя состояние этой сущности.
     *
     * @param DomainEventInterface $event Событие предметной области
     * @return void
     */
    public function apply(DomainEventInterface $event): void
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

        if (!isset($trace[1]['class']) || $trace[1]['class'] !== $this->aggregateClass) {
            throw new EntityMethodCallOutsideAggregateContextException(
                message: 'Метод сущности не может быть вызван вне контекста агрегата'
            );
        }

        if (!method_exists($this, 'apply' . $event->eventType)) {
            throw new DomainEventNotImplementedException(message: "Событие {$event->eventType} не реализовано");
        }

        $this->{'apply' . $event->eventType}($event);
    }
}
