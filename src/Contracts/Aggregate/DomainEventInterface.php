<?php

declare(strict_types=1);

namespace Dclouds\Ddd\Contracts\Aggregate;

use Dclouds\Ddd\ValueObjects\Uuid\Uuid;

/**
 * Доменное событие, представляющее значимое изменение состояния в предметной области.
 *
 * Передается агрегату для изменения состояния его сущностей.
 *
 * Соответствует концепции Domain Events из DDD и является основным типом событий
 * в событийно-ориентированном проектировании (EDD).
 */
interface DomainEventInterface
{
    /**
     * Идентификатор агрегата.
     *
     * Служит для определения в рамках внешней системы конкретной сущности, для которой
     * было зарегистрировано данное событие.
     */
    public ?Uuid $aggregateId {get;}

    /**
     * Идентификатор события
     */
    public Uuid $eventId {get;}

    /**
     * @var positive-int Версия события
     */
    public int $eventVersion {get;}

    /**
     * @var non-empty-string Тип события (название события)
     */
    public string $eventType {get;}
}
