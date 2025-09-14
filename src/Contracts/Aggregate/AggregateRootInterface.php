<?php

declare(strict_types=1);

namespace Dclouds\Ddd\Contracts\Aggregate;

use Dclouds\Ddd\Contracts\Aggregate\Exceptions\DomainEventNotImplementedException;
use Dclouds\Ddd\Contracts\Aggregate\Exceptions\ImpossibleToReleaseDomainEventsWithoutAggregateVersionUpdateException;
use Dclouds\Ddd\Contracts\Aggregate\Exceptions\ImpossibleToReleaseIntegrationEventsException;
use Dclouds\Ddd\Contracts\Aggregate\Exceptions\ImpossibleToReleaseNonIntegrationEventsException;
use Dclouds\Ddd\Contracts\Aggregate\Exceptions\IncorrectAggregateVersionException;
use Dclouds\Ddd\ValueObjects\Uuid\Uuid;

/**
 * Корень агрегата (Aggregate Root) в соответствии с DDD.
 *
 * Отвечает за управление потоком событий в агрегате:
 * - Фиксирует произошедшие события
 * - Обеспечивает их применение для изменения состояния
 * - Контролирует выпуск событий во внешнюю систему
 *
 * Реализует паттерн Event Sourcing и методологию EDD для
 * управления изменениями состояния.
 */
interface AggregateRootInterface
{
    /**
     * Идентификатор агрегата.
     */
    public Uuid $id {get;}

    /**
     * Версия агрегата.
     *
     * @throws IncorrectAggregateVersionException
     */
    public ?int $version {get;}

    /**
     * Флаг указывающий на то, были ли примнены какие-либо
     * события к агрегату
     */
    public bool $isModified {get;}

    /**
     * Фиксирует событие в агрегате перед последующим применением.
     * Событие добавляется в очередь необработанных событий агрегата.
     *
     * @param DomainEventInterface $event Событие
     * @return void
     */
    public function recordEvent(DomainEventInterface $event): void;

    /**
     * Возвращает все необработанные события предметной области (Domain events)
     * и очищает внутреннюю очередь.
     *
     * Используется для публикации внутренних событий для Event Sourcing.
     *
     * @return list<DomainEventInterface> Массив событий
     * @throws ImpossibleToReleaseDomainEventsWithoutAggregateVersionUpdateException
     */
    public function releaseDomainEvents(): array;

    /**
     * Возвращает все необработанные интеграционные события (Integration events)
     * и очищает внутреннюю очередь.
     *
     * Используется для публикации событий во внешние источники.
     *
     * @return list<IntegrationEventInterface> Массив событий
     * @throws ImpossibleToReleaseIntegrationEventsException
     * @throws ImpossibleToReleaseNonIntegrationEventsException
     */
    public function releaseIntegrationEvents(): array;

    /**
     * Применяет событие к агрегату, изменяя его состояние. Выполняет проверку инварианта
     * агрегата и добавляет событие во внутреннюю очередь.
     *
     * @param DomainEventInterface $event Событие
     * @return void
     *
     * @throws DomainEventNotImplementedException
     */
    public function apply(DomainEventInterface $event): void;
}
