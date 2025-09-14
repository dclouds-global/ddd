<?php

declare(strict_types=1);

namespace Dclouds\Ddd\Contracts\Outbox;

use Dclouds\Ddd\Contracts\Aggregate\IntegrationEventInterface;
use Dclouds\Ddd\Outbox\Exceptions\OutboxException;

/**
 * Сервис для работы с Outbox
 */
interface OutboxServiceInterface
{
    /**
     * Создает в Outbox запись в статусе 'pending'
     * для каждого listener'а передаваемого события
     *
     * @throws OutboxException
     */
    public function registerEvent(IntegrationEventInterface $event, string $producer): void;

    /**
     * Переводит передаваемое событие в статус inProgress
     * для дальнейшей обработки
     *
     * @throws OutboxException
     */
    public function processEvent(IntegrationEventInterface $event, string $producer): void;

    /**
     * Пометить событие в Outbox как успешно завершенное
     *
     * @throws OutboxException
     */
    public function markAsSuccess(IntegrationEventInterface $event, string $producer): void;

    /**
     * Пометить событие в Outbox как проваленное
     *
     * @throws OutboxException
     */
    public function markAsFailed(IntegrationEventInterface $event, string $producer): void;

    /**
     * Удалить все события Outbox завершенные успешно и созданные
     * ранее указанной метки времени
     *
     * @throws OutboxException
     */
    public function removeOldSuccessEvents(int $timestamp): void;

    /**
     * Получить список проваленных outbox записей событий
     *
     * @param array<string> $types Конкретные типы событий, по которым нужно вернуть записи. По умолчанию все.
     * @param array<string> $ids Конкретные id событий, по которым нужно вернуть записи. По умолчанию все.
     * @param array<string> $producers Конкретные listener'ы для которых нужно вернуть записи. По умолчанию все.
     * @param int $maxTries Максимальное количество попыток обработки, до которого нужно вернуть записи. По умолчанию 4.
     *
     * @return array<OutboxRecordInterface>
     *
     * @throws OutboxException
     */
    public function getFailedEventRecords(array $types = [], array $ids = [], array $producers = [], int $maxTries = 4): array;
}
