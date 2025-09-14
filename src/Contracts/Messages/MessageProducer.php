<?php

declare(strict_types=1);

namespace Dclouds\Ddd\Contracts\Messages;

/**
 * Интерфейс для отправки сообщений
 */
interface MessageProducer
{
    /**
     * Отправить сообщение
     */
    public function produceMessage(Message $message): void;
}
