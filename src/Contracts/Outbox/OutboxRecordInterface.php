<?php

declare(strict_types=1);

namespace Dclouds\Ddd\Contracts\Outbox;

use Dclouds\Ddd\Outbox\Enums\OutboxStatusEnum;

interface OutboxRecordInterface
{
    /**
     * @var non-empty-string Идентификатор события
     */
    public string $eventId {get;}

    /**
     * @var non-empty-string Тип события (название события)
     */
    public string $eventType {get;}

    /**
     * @var non-empty-string Сериализованное тело события
     */
    public string $eventBody {get;}

    /**
     * @var class-string Название класса продюсера события
     */
    public string $producer {get;}

    /**
     * @var OutboxStatusEnum Статус обработки события конкретным продюсером
     */
    public OutboxStatusEnum $status {get;}

    /**
     * @var int Количество попыток обработки события конкретным продюсером
     */
    public int $triesCount {get;}
}
