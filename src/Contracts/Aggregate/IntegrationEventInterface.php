<?php

declare(strict_types=1);

namespace Dclouds\Ddd\Contracts\Aggregate;

/**
 * Интеграционное событие, представляющее значимое изменение состояния в предметной области.
 *
 * Используется для уведомления внешних систем об изменении состояния ягрегата. Генерируется
 * агрегатом, основываясь на переданных в него событиях предметной области (Domain Events) после
 * того, как эти события были выпущены метдом: @see AbstractAggregateRoot::releaseDomainEvents()
 */
interface IntegrationEventInterface extends DomainEventInterface
{
    /**
     * Версия агрегата.
     *
     * Служит для определения версии агрегата, для которого было зарегистрировано данное событие.
     */
    public ?int $aggregateVersion {get;}
}
