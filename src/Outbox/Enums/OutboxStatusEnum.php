<?php

declare(strict_types=1);

namespace Dclouds\Ddd\Outbox\Enums;

/**
 * Допустимые статусы Outbox событий
 */
enum OutboxStatusEnum: string
{
    /**
     * В ожидании. Выставляется сразу после создания события.
     */
    case Pending = 'pending';

    /**
     * В процессе. Выставляется на время выполнения события.
     */
    case InProgress = 'inProgress';

    /**
     * Успешно. Выставляется после успешного завершения забытия.
     */
    case Success = 'success';

    /**
     * Неуспешно. Выставляется в случае, если событие не было
     * выполнено успешно за отведенное количество попыток.
     */
    case Failed = 'failed';
}
