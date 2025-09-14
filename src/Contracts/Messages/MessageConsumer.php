<?php

declare(strict_types=1);

namespace Dclouds\Ddd\Contracts\Messages;

/**
 * Интерфейс для обработки сообщений
 */
interface MessageConsumer
{
    /**
     * Обработать сообщение
     */
    public function consumeMessage(Message $message): void;
}
