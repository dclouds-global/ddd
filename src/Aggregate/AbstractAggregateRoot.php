<?php

declare(strict_types=1);

namespace Dclouds\Ddd\Aggregate;

use Closure;
use Dclouds\Ddd\Contracts\Aggregate\AggregateRootInterface;
use Dclouds\Ddd\Contracts\Aggregate\DomainEventInterface;
use Dclouds\Ddd\Contracts\Aggregate\Exceptions\DomainEventNotImplementedException;
use Dclouds\Ddd\Contracts\Aggregate\Exceptions\ImpossibleToChangeVersionForUnmodifiedAggregateException;
use Dclouds\Ddd\Contracts\Aggregate\Exceptions\ImpossibleToReleaseDomainEventsWithoutAggregateVersionUpdateException;
use Dclouds\Ddd\Contracts\Aggregate\Exceptions\ImpossibleToReleaseIntegrationEventsException;
use Dclouds\Ddd\Contracts\Aggregate\Exceptions\ImpossibleToReleaseNonIntegrationEventsException;
use Dclouds\Ddd\Contracts\Aggregate\Exceptions\IncorrectAggregateVersionException;
use Dclouds\Ddd\Contracts\Aggregate\IntegrationEventInterface;
use Dclouds\Ddd\ValueObjects\Uuid\Uuid;

/**
 * Базовый класс корневого агрегата
 */
abstract class AbstractAggregateRoot implements AggregateRootInterface
{
    /**
     * Версия агрегата. При установке нового значения - новая
     * версия должна быть выше текущей.
     */
    public ?int $version = null {
        get => $this->version;
        set(?int $version) {
            if (!$this->isModified) {
                throw new ImpossibleToChangeVersionForUnmodifiedAggregateException(
                    message: 'Невозможно изменить версию для немодифицированного агрегата'
                );
            }
            if ($version <= $this->version) {
                throw new IncorrectAggregateVersionException(
                    message: 'Новая версия агрегата должна быть больше текущей'
                );
            }
            $this->isVersionUpdated = true;
            $this->version = $version;
        }
    }

    /**
     * @var list<DomainEventInterface> Массив событий предметной области
     */
    protected array $domainEvents = [];

    /**
     * @var list<mixed> Массив интеграционных событий.
     */
    protected array $integrationEvents = [];

    /**
     * Флаг, указывающий была бы обновлена версия агрегата.
     */
    protected bool $isVersionUpdated = false;

    /**
     * Флаг указывающий на то, были ли примнены какие-либо
     * события к агрегату.
     */
    protected(set) bool $isModified = false;

    /**
     * Проверка составных бизнес-правил
     *
     * @return void
     */
    abstract protected function validate(): void;

    public function recordEvent(DomainEventInterface $event): void
    {
        if (is_null($event->aggregateId)) {
            Closure::bind(function (Uuid $id) {
                /** @phpstan-ignore-next-line */
                $this->aggregateId = $id;
            }, $event, $event::class)($this->id);

            $this->domainEvents[] = $event;
            $this->isModified = true;
        }
    }

    public function releaseDomainEvents(): array
    {
        if (!$this->isVersionUpdated) {
            throw new ImpossibleToReleaseDomainEventsWithoutAggregateVersionUpdateException(
                message: 'Невозможно выпустить события предметной области не обновив версию агрегата'
            );
        }

        $this->integrationEvents = $this->domainEvents;
        $this->domainEvents = [];
        $this->isVersionUpdated = false;

        return $this->integrationEvents;
    }

    public function releaseIntegrationEvents(): array
    {
        $numOfDomainEvents = count($this->domainEvents);
        $numOfIntegrationEvents = count($this->integrationEvents);
        if ($numOfDomainEvents > 0 || $numOfIntegrationEvents === 0) {
            throw new ImpossibleToReleaseIntegrationEventsException(
                message: 'Нельзя выпустить интеграционные события, пока не были выпущены события предметной области'
            );
        }

        foreach ($this->integrationEvents as $event) {
            if (!$event instanceof IntegrationEventInterface) {
                throw new ImpossibleToReleaseNonIntegrationEventsException(
                    message: 'Нельзя выпустить события, которые не являются интеграционными'
                );
            }
        }

        $release = [];
        for ($i = 0; $i < $numOfIntegrationEvents; $i++) {
            Closure::bind(function (int $version) {
                $this->aggregateVersion = $version;
            }, $this->integrationEvents[$i], $this->integrationEvents[$i]::class)($this->version);
            $release[] = $this->integrationEvents[$i];
            unset($this->integrationEvents[$i]);
        }
        return $release;
    }

    public function apply(DomainEventInterface $event): void
    {
        $applyMethodPostfix = ($event->eventVersion > 1) ? ('V' . $event->eventVersion) : '';
        $methodName = 'apply' . $event->eventType . $applyMethodPostfix;

        if (!method_exists($this, $methodName)) {
            throw new DomainEventNotImplementedException(message: "Событие {$event->eventType} не реализовано");
        }

        $this->recordEvent($event);
        $this->{$methodName}($event);
        $this->validate();
    }
}
