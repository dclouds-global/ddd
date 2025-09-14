<?php

declare(strict_types=1);

namespace Dclouds\Ddd\Aggregate;

use Dclouds\Ddd\Contracts\Aggregate\IntegrationEventInterface;
use Dclouds\Ddd\ValueObjects\Uuid\Uuid;

/**
 * Базовый класс события.
 */
abstract class AbstractEvent implements IntegrationEventInterface
{
    /**
     * Идентификатор агрегата.
     *
     * Устанавливается автоматически после передачи в
     * метод `apply` агрегата.
     */
    protected(set) ?Uuid $aggregateId = null;

    /**
     * @var positive-int Версия события.
     *
     * В случае изменений в бизнес-логике, влияющих на реализацию конкретного
     * события, само событие не должно быть изменено, так как это может привести
     * к невозможности восстановления агрегата для Event Sourcing модулей. При
     * этом тип события (базирующийся на имени класса события) может иметь
     * значение при отображении в истории изменений, поэтому не рекомендуется
     * создавать новое событие (с новым типом), реализующее измененную логику.
     *
     * Лучшим решением будет создать одноименное событие в другом неймспейсе
     * (например Aggregate\Events\V2). Метод агрегата для обработки события
     * с версией отличной от 1 должен иметь постафикс VersionN, где N - номер
     * версии события.
     */
    protected(set) int $eventVersion = 1;

    /**
     * Версия агрегата для которого данное событие устанавливается
     */
    protected(set) ?int $aggregateVersion = null;

    /**
     * Идентификатор события.
     *
     * Генерируется автоматически при создании экземпляра класса события.
     */
    protected(set) Uuid $eventId {
        get {
            if (!isset($this->eventId)) {
                $this->eventId = Uuid::generate();
            }

            return $this->eventId;
        }

        set (Uuid $uuid) {
            $this->eventId = $uuid;
        }
    }

    /**
     * @var non-empty-string Тип события/
     *
     * Устанавливается автоматически при создании экземпляра класса события
     * и соответствует имени класса этого события без учета неймспейса.
     */
    protected(set) string $eventType {
        get {
            if (!isset($this->eventType)) {
                $this->eventType = array_reverse(explode('\\', get_class($this)))[0];
            }
            return $this->eventType;
        }

        set(string $eventType) {
            $this->eventType = $eventType;
        }
    }
}
